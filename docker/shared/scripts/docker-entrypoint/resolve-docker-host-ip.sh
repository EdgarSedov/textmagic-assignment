#!/bin/sh

# fix for host.docker.internal not existing on linux https://github.com/docker/for-linux/issues/264
# see https://dev.to/bufferings/access-host-from-a-docker-container-4099
HOST_DOMAIN="host.docker.internal"

APPEND_COMMAND="tee -a /etc/hosts"
if sudo -n true; then
    APPEND_COMMAND="sudo $APPEND_COMMAND"
fi

# check if the host exists
#see https://stackoverflow.com/a/24049165/413531
if dig ${HOST_DOMAIN} | grep -q 'NXDOMAIN'; then
    # on linux, it will fail - so we'll "manually" add the hostname in the host file
    HOST_IP=$(ip route | awk 'NR==1 {print $3}')
fi

if [ -z "$HOST_IP" ]; then
    HOST_IP="$(ping -c 1 ${HOST_DOMAIN} | head -2 | tail -1 | awk '{print $5}' | sed 's/[(:)]//g')"
fi

echo "$HOST_IP $HOST_DOMAIN" | $APPEND_COMMAND
echo "$HOST_IP $APP_HOST" | $APPEND_COMMAND

for i in "$@"; do
    echo "$i"
done

trap 'kill -TERM $PID' TERM INT
exec "$@" &
PID=$!
wait $PID
wait $PID
