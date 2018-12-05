# Portfolio2Leo1

<h1>Introduction</h1>

For this assigment we have created 2 containers with LXC.
The first container is named "server" and it hosts a lighttpd server with the index.php file in the directory /var/wwww/localhost/htdocs.
The second container is named "sendNumber" and in the /bin directory there is the sh file named "rng" which provides random numbers.

<h1>Creation of containers</h1>

The containers are created as follow:

<code>lxc-create -n server -t download -- -d alpine -r 3.4 -a armhf</code><br/>
<code>lxc-create -n sendNumber -t download -- -d alpine -r 3.4 -a armhf</code>

then we start the server container:<br/>
<code>lxc-start -n server</code><br/>
Then we open the server console:<br/>
<code>lxc-attach -n server</code>

Then we update package list and install needed packages in the container:<br/>
<code>apk update</code><br/>
<code>apk add lighttpd php5 php5-cgi php5-curl php5-fpm</code><br/>

then again in the server container, we  uncomment the include "mod_fastcgi.conf" line in /etc/lighttpd/lighttpd.conf

And we start the lighttpd service:<br/>
<code>rc-update add lighttpd default<br/>
openrc</code>

In the sendNumber container we create the file rng.sh in /bin directory.

<h1>Setting bridge</h1>

Creating bridge between the containers to let them communicate to each other:<br/>
In the directory /etc/lxc we create a default.conf file with this content:
 
<code>lxc.network.type = veth<br/>
lxc.network.link = lxcbr0<br/>
lxc.network.flags = up<br/>
lxc.network.hwaddr = 00:16:3e:xx:xx:xx</code>

Where lxcbr0 is the name of our bridge on the host.

Then we create the file /etc/default/lxc-net with this content:

<code>USE_LXC_BRIDGE="true"</code>

And the bridge is created!

<h1>Access the server container from outside</h1>

Finally we have mapped a port from the raspberry host's public interface (enx00e04c534458) to the server container's IP (10.0.3.251):

<code>sudo iptables -t nat -A PREROUTING -i enx00e04c534458 -p tcp --dport 80 -j DNAT --to-destination 10.0.3.251:8080</code>

We can now reach our server container's port 8080 through our raspberry host's 80 port.

And we have executed the socat command:<br/>
<code>socat -v -v tcp-listen:8080,fork,reuseaddr exec:"sh /bin/rng.sh"</code><br/>
In the sendNumber container to send random numbers to the server webpage in the server container through 8080 port.
With the public raspberry's IP we can reach the webpage with random numbers displayed.

