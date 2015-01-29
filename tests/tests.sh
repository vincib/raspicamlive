cd $(readlink -f $( dirname "${BASH_SOURCE[0]}" ))

source assert.sh

assert_raises "which ffmpeg" 0

assert_raises "/usr/bin/raspistill -t 1 -o /tmp/.test.jpg" 0

# end of test suite
assert_end functional
