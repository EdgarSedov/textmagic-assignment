[Technical assessment](TASK.MD)

All project setup was tested only in Linux, Apple and Win may or may not require adjustments.
To test and run this assessment we need Docker, Traefik and bindings for `textmagic.local` in the hosts file.

## Setup

### Traefik

If you already have traefik up and running - all you need to do is either make sure you use `traefik-public` network or
modify the `docker-compose.yml` file in this repository to use your traefik network name.

If you don't have traefik up and running yet - all you need to do is to make sure ports 80 and 443 are unoccupied on
the host machine and run the command `make traefik-install`, which will create docker network named `traefik-public` and
run the traefik from the provided `traefik.docker-compose.yml` file. Don't forget to remove that network later if you
don't need it by `docker network rm traefik-public`.

### Bindings

We need to edit hosts file to point needed hosts locally:

`echo '127.0.0.1 textmagic.local' | sudo tee -a /etc/hosts`

### Project setup

Run `make install` and wait for project to be ready. Then proceed to http://textmagic.local
