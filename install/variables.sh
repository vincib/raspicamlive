BASH_NAME=${0##*/}
MY_PID=$(echo $$)
APP_PATH=$(readlink -f $( dirname "${BASH_SOURCE[0]}" )"/../")
