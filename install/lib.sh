

##
 # We need an extX disk: format the drive and makefs
 # We use a backup of the previous partition, which we don't erase
 # @param $1 string a block device, default "/dev/sda"
##
format_usb(){
    local BACKUP_FILE="/tmp/sda-partition-sectors.save"
    local DISK="$1"
    [ ! -z $DISK ] || DISK="/dev/sda"

    # Make a copy of the backup if exists
    backup_file $BACKUP_FILE

    # Save the sectors
    sfdisk -d /dev/sda > "$BACKUP_FILE"

    # Format to max size
    sfdisk $DISK << EOF
;
EOF

    # In case of error, restore partition
    if [ $? -ne 0 ] ; then
        warn "Something went wrong. Restoring partition"
        cat "$BACKUP_FILE" | sfdisk /dev/sda
        return 1
    fi;

    
    # Attempt to create FS on the new partition
    # In case of error, restore partition
    mkfs.ext4 "${DISK}1"
    if [ $? -ne 0 ] ; then
        warn "Something went wrong. Restoring partition"
        cat "$BACKUP_FILE" | sfdisk /dev/sda
        return 1
    fi;

    return 0
}


##
 # We need to automount the USB disk, add it to fstab
 # @param $1 string a block device, default "/dev/sda1"
 # @param $2 string a mount point, default "/mnt"
##
edit_fstab(){

    local DISK="$1"
    [ ! -z $DISK ] || DISK="/dev/sda1"

    local MOUNT_POINT="$2"
    [ ! -z $MOUNT_POINT ] || MOUNT_POINT="/mnt"
    
    # Write to fstab
    echo "# Added by raspicamlive:install.sh for USB disk automount" >> /etc/fstab
    echo "${DISK}  ${MOUNT_POINT}              ext4    defaults,noatime  0  0 " >> /etc/fstab

    return 0
}
