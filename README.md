# Portfolio2Leo1

For this assigment we have created 2 containers with LXC.
The first container is named "server" and it hosts a lighttpd server with the index.php file in the directory /var/wwww/localhost/htdocs.
The second container is named "sendNumber" and in the /bin directory there is the sh file named "rng" which provides random numbers.
\n
The containers are created as follow:

lxc-create -n server -t download -- -d alpine -r 3.4 -a armhf
lxc-create -n sendNumber -t download -- -d alpine -r 3.4 -a armhf

then we start the server container:
lxc-start -n server
Then we open the server console:
lxc-attach -n server

Then we update package list and install needed packages in the container:
apk update
apk add lighttpd php5 php5-cgi php5-curl php5-fpm

then again in the server container, we  uncomment the include "mod_fastcgi.conf" line in /etc/lighttpd/lighttpd.conf

And we start the lighttpd service:
rc-update add lighttpd default
openrc

In the sendNumber container we create the file rng.sh in /bin directory.

Creating bridge between the containers to let them communicate to each other:
 In the directory /etc/lxc we create a default.conf file with this content:
 
lxc.network.type = veth
lxc.network.link = lxcbr0
lxc.network.flags = up
lxc.network.hwaddr = 00:16:3e:xx:xx:xx

Where lxcbr0 is the name of our bridge on the host.

Then we create the file /etc/default/lxc-net with this content:

USE_LXC_BRIDGE="true"

And the bridge is created!

Finally we have mapped a port from the raspberry host's public interface (enx00e04c534458) to the server container's IP (10.0.3.251):

sudo iptables -t nat -A PREROUTING -i enx00e04c534458 -p tcp --dport 80 -j DNAT --to-destination 10.0.3.251:8080

We can now reach our server container's port 8080 through our raspberry host's 80 port.

And we have executed the socat command:
socat -v -v tcp-listen:8080,fork,reuseaddr exec:"sh /bin/rng.sh"
In the sendNumber container to send random numbers to the server webpage in the server container through 8080 port.
With the public raspberry's IP we can reach the webpage with random numbers displayed.

