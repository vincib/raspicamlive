
export TEXTDOMAIN=raspicamlive-install
export TEXTDOMAINDIR=$(pwd)/translations


# Colors
COL_GRAY="\x1b[30;01m"
COL_RED="\x1b[31;01m"
COL_GREEN="\x1b[32;01m"
COL_YELLOW="\x1b[33;01m"
COL_BLUE="\x1b[34;01m"
COL_PURPLE="\x1b[35;01m"
COL_CYAN="\x1b[36;01m"
COL_WHITE="\x1b[37;01m"
COL_RESET="\x1b[39;49;00m"


E_CDERROR=666

# Output & Translations utilities 
# @see http://mywiki.wooledge.org/BashFAQ/098
# @see http://www.linuxtopia.org/online_books/advanced_bash_scripting_guide/localization.html

debug() {

    echo -e $COL_PURPLE;
    local format="$1"
    shift
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@" # >&1
    echo -e $COL_RESET;
}

misc() {
    
    echo -e $COL_GRAY;
    local format="$1"
    shift
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@" # >&1
    echo -e $COL_RESET;

}
ask() {
    echo -e $COL_WHITE;
    local format="$1"
    shift
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@" # >&1
    echo -e $COL_RESET;

}

info() {
    
    echo -e $COL_GREEN;
    local format="$1"
    shift
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@"
    echo -e $COL_RESET;

}

warn() {

    echo -e $COL_RED;
    local format="$1"
    shift
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@" 
    echo -e $COL_RESET;

}

alert() {

    echo -e $COL_RED;
    local format="$1"
    shift
    printf "\n"
    printf "$(gettext 'A critical error occured: ' )" 
    printf "$(gettext -d $TEXTDOMAIN -s "$format")" "$@" 
    printf "\n"
    printf "$(gettext 'Exiting.'  )" 
    printf "\n"
    echo -e $COL_RESET;
    exit $E_CDERROR

}


spacer() {
    
    echo -e $COL_GRAY;
    echo -e " - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -"
    echo -e $COL_RESET;

}

### Various utilities


## wraps apt-get
apt_get() {
    local PACKAGE="$1"
    local RES
    local CMD
    if [[ $DRY_RUN = 1 ]] ; then
        debug "System should install %s" $@
        return 0
    fi;
    # Installing 
    dpkg-query -s "$PACKAGE" 1>/dev/null 
    RES=$?
    # Skip : package installed
    if [ 0 -eq $RES ]; then
        [[ $DEBUG != 1 ]] || debug "%s already installed" $PACKAGE
        return 0
    fi
    info "Installing %s" $PACKAGE
    apt-get install -y --force-yes $PACKAGE 1>/dev/null 

}



# gatepoint for all 'y,o' user inputs management
validate() {
    local VAR=$1
    if [[ "n" == ${VAR,,} ]] ; then
        return 0;
    fi;
    return 1;
}

# Encapsulates cp 
# @param 1 source file
# @param 2 target file 
copy(){
    if [[ $DRY_RUN = 1 ]] ; then
        debug "System copies %s as %s" "$1" "$2" 
    else
            if [[ $DEBUG = 1 ]] ; then 
                    debug "cp %s %s" "$1" "$2" 
            fi;    
            ensure_file_exists "$1"  
            ensure_file_path_exists "$2"
            if [ -f $2 ] ; then
                info "$2 already exists." 
                backup_file "/etc/hostname"
            fi
            cp "$1" "$2"
    fi;
}

# Makes sure a necessary file exists, or exits  
# @param 1 a file path
ensure_file_exists(){
    if [[ $DRY_RUN = 1 ]] ; then
        debug "System makes sure file %s  exists" "$1"
    else
        if [[ $DEBUG = 1 ]] ; then 
            debug "Checking file %s exists" "$1"
        fi;    
        if [[ ! -f "$1" ]] ; then
            alert "File %s does not exist" "$1"
        fi;
    fi;
}

# Creates folders path for file if necessary  
# @param 1 a file path
ensure_file_path_exists(){
    if [[ $DRY_RUN = 1 ]] ; then
        debug "System makes sure path for %s  exists" "$1"
    else
        local dir_path=$(echo "$1" | sed -e "s/\(.*\)\/.*/\1/")
        if [[ -d "$dir_path" ]] ; then 
            return 1
        fi
        if [[ -f "$dir_path" ]] ; then 
            warn "Failed to create %s as it is a file already" "$dir_path"
            return 0
        fi
        if [[ $DEBUG = 1 ]] ; then 
            debug "Creating folder %s for file %s" "$dir_path" "$1"
        fi;    
        mkdir -p "$dir_path"
    fi;
}

# Encapsulates rm 
# @param 1 file
delete(){
    if [[ $DRY_RUN = 1 ]] ; then
        debug "System deletes %s" "$1"
        return 1
    fi
    # If no file, exit
    if [ ! -f "$1" ] ; then
        return 1
    fi
    if [[ $DEBUG = 1 ]] ; then 
        debug "Deleting %s" "$1"
    fi;    
    rm -f $1
    return 1
}

# Encapsulates echo $1 > $2
# @param 1 content
# @param 2 file
write() {
    
    if [[ $DRY_RUN == 1 ]] ; then
        debug "System writes '%s' \nin %s" "$1" "$2"
    else
        if [[ $DEBUG == 1 ]] ; then 
            debug "Writing '%s' \nin %s" "$1" "$2"
        fi;    
        # backups file if exists
        backup_file "$2"
        # touch file
        rm -f "$2"
        touch "$2"
        # echo each text line
        # in a subshell to not mess IFS
        $(IFS="
";for line in $(echo "$1"); do echo $line >> $2; done;)
    fi;
    
}

# inserts a line in file at line number
# @param 1 file path
# @param 2 line #
# @param 3 line
insert(){
    if [[ $DRY_RUN == 1 ]] ; then
        debug "Systems inserts '%s' in %s at line #%s" "$3" "$1" "$2" 
        return 1
    fi;
    sed -i "$2 i\
$3"     $1
    return 1
    
}

# replaces string $1 by $2 in $3
# @param 1 regexp
# @param 2 replacement
# @param 3 file path
replace(){
    if [[ $DRY_RUN == 1 ]] ; then
        debug "Systems replaces '%s' by %s in %s" "$1" "$2" "$3"
        return 1
    fi;
    if [[ $DEBUG == 1 ]] ; then 
        debug "Replacing '%s' by %s in %s" "$1" "$2" "$3"
    fi;
    sed -i -e "s/$1/$2/g" "$3"
    return 1
    
}

# backups file if exists
# @param 1 file path
backup_file(){
    if [[ $DRY_RUN == 1 ]] ; then
        debug "Systems makes a backup of %s" "$1"
        return 1
    fi;
    if [ -f "$1" ] ; then
        local backed=0
        local num=1
        while [[ $backed != 1 ]] ; do 
            if [ -f "$1.$num" ] ; then
                num=$(( $num + 1 ))
            else
                cp "$1" "$1.$num"
                touch "$1"
                backed=1
            fi;
        done;
        if [[ $DEBUG == 1 ]] ; then 
            debug "File %s backed as %s.$num" "$1" "$1"
        fi;
        return 1
    fi;
    return 0
}

# Attempts to check if a service is currently running 
# @param 1     the service name ex: mysqld
#            This must be an /etc/init.d script name
check_service() {
    if [ -z $1 ] ; then
        alert "Missing service name %s" "$1"
    fi;
    local service=$1
    if [ $(pgrep $1 | wc -l) -eq 0 ] ; then
        warn "Service $service is not running"
    else
        info "Service $service is running OK"
    fi;    
}

