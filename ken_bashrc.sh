alias 'ssh'='~/ken_scripts/ssh_wrapper.sh'
alias redis-vim='/home/kmitchner/ken_scripts/redis-vim'
alias php='/home/kmitchner/ken_scripts/php_wrapper.sh'

# If no SSH agent is already running, start one now. Re-use sockets so we never
# have to start more than one session.

export SSH_AUTH_SOCK=~/.ssh-socket

ssh-add -l >/dev/null 2>&1

if [ $? = 2 ]; then
    # No ssh-agent running
    echo "Starting ssh-agent"
    rm -rf $SSH_AUTH_SOCK
    TMP=$(mktemp)
    ssh-agent -a $SSH_AUTH_SOCK >$TMP
    source $TMP
    echo $SSH_AGENT_PID > ~/.ssh-agent-pid
    rm $TMP
    ssh-add
fi
