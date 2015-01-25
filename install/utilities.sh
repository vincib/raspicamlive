cp_check(){

    if [ -f $1 ] ; then
	echo "$1 already exists." 
	return 1
    fi
    cp "$1" "$2"
}
