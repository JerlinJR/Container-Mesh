FROM ubuntu:23.04
ENV DEBIAN_FRONTEND=noninteractive

# Install required packages
RUN apt-get -y -q update && \
    apt-get -y upgrade && \
    apt-get install -y apt-utils git && \
    apt-get install -y systemd openssh-server nano htop build-essential software-properties-common python3-dev python3-pip apache2 php libapache2-mod-php php-mysql && \
    apt-get install -y sudo wireguard && \
    apt-get clean

RUN apt install jq -y

RUN apt install net-tools -y

RUN apt install curl -y

RUN apt install -y iproute2

RUN apt-get install ipcalc -y

# RUN sed -i 's#/var/www/html#/home/htaccess#g' /etc/apache2/sites-available/000-default.conf \
#     && sed -i 's#/var/www/html#/home/htdocs#g' /etc/apache2/apache2.conf

RUN mkdir /home/htdocs /home/htaccess
RUN chown -R www-data:www-data /home/htdocs
COPY apache/index.html /home/htdocs/html/

# Set up SSH
RUN sed -ri 's/^#?PermitRootLogin\s+.*/PermitRootLogin yes/' /etc/ssh/sshd_config && \
    echo 'root:root' | chpasswd 
    # useradd -ms /bin/bash jerlin && \
    # echo 'jerlin:jerlin' | chpasswd

# Set up WireGuard
COPY scripts/setupwg.sh /bin

COPY scripts/wg0.conf.template /etc/wireguard/wg0.conf.template
COPY scripts/entrypoint.sh /usr/local/bin/entrypoint.sh

# Allow execution of the script
RUN chmod +x /usr/local/bin/entrypoint.sh
RUN chmod +x /bin/setupwg.sh

COPY apache/000-default.conf /home/htaccess/

# Enable IP forwarding
RUN echo "net.ipv4.ip_forward=1" >> /etc/sysctl.conf && sysctl -p
RUN rm /etc/apache2/apache2.conf
COPY apache/apache2.conf /etc/apache2/

# CMD ["bash", "-c", "sudo wg-quick up wg0 && /lib/systemd/systemd"]
CMD ["/usr/local/bin/entrypoint.sh"]
# CMD ["/sbin/init"]
# CMD ["/lib/systemd/systemd"]

EXPOSE 22
