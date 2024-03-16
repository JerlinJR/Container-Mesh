import argparse
import docker
import time
from prettytable import PrettyTable
import subprocess
import sys
import json

class Docker:
    def __init__(self, name):
        self.name = name
        self.client = docker.from_env()

    def run_image(self, detach=True, privileged=True):
        try:
            t = time.time()
            time.sleep(1)
            volume_mount = {self.name: {'bind': "/home", 'mode': 'rw'}} if self.name else None          
            container = self.client.containers.run(
                "mesh:latest",
                name=self.name,
                detach=detach,
                hostname="Container-Mesh",
                privileged=privileged,  # Set privileged mode
                volumes=volume_mount  # Mount the volume if provided
            )
            print("[+]Setting " + self.name + " to privileged mode.")
            time.sleep(2)
            print("[+]Container started with ID: " + container.id)
            return container
        except docker.errors.APIError as e:
            print(f"ERROR: {e.explanation}")
            print(f"Status code: {e.response.status_code}")
            sys.exit(1)
        except Exception as e:
            # Handle other unexpected exceptions
            print(f"Unexpected error: {e}")
            sys.exit(1)


    def get_container_logs(self):
        try:
            container = self.client.containers.get(self.name)
        except docker.errors.NotFound:
            print(f"Container with name '{self.name}' not found.")
            return

        logs = container.logs()
        return logs

    def stop_and_remove(self):
        # Stop and remove a container by ID or name
        try:
            print(f"[-]Sending stop signal to container: {self.name}")
            time.sleep(2)
            print(f"[-]Removing wireguard configuration")
            time.sleep(2)
            print(f"[-]Preserving home directory")
            container = self.client.containers.get(self.name)
        except docker.errors.NotFound:
            print(f"Container with ID or name '{self.name}' not found.")
            return
        container.stop()
        container.remove()
        print(f"[-]Container stopped and removed successfully")

    def create_user(self):
        client = docker.from_env()
        try:
            container_id = client.containers.get(self.name).id
            container = client.containers.get(container_id)
            container.exec_run(['adduser', '--disabled-password', '--gecos', '', self.name])
            container.exec_run(['bash', '-c', f'echo "{self.name}:{self.name}@123" | chpasswd'])
            container.exec_run(['adduser', self.name, 'sudo'])

            print(f"[+]Username: {self.name}")
            print(f"[+]Password: {self.name}@123")
            time.sleep(1)
        except docker.errors.NotFound:
            print(f"Container with name '{self.name}' not found.")
        except Exception as e:
            print(f"An error occurred: {e}")

    def getIP(self):
        container_ids = subprocess.check_output(["docker", "ps", "-q"]).decode("utf-8").split()
        container_info = []

        for container_id in container_ids:
            container_name = (
                subprocess.check_output(["docker", "inspect", "--format", "{{.Name}}", container_id])
                .decode("utf-8")
                .strip()[1:]
            )
            try:
                wg_ip = (
                    subprocess.check_output(
                        ["docker", "exec", container_id, "ip", "-o", "-4", "addr", "show", "wg0"]
                    )
                    .decode("utf-8")
                    .split()[3]
                    .split("/")[0]
                )
                container_info.append({
                    "Container ID": container_id,
                    "Container Name": container_name,
                    "WireGuard IP": wg_ip
                })
            except subprocess.CalledProcessError:
                container_info.append({
                    "Container ID": container_id,
                    "Container Name": container_name,
                    "WireGuard IP": "Not found"
                })

        json_output = json.dumps(container_info, indent=2)
        print(json_output)
        return json_output
    def list_containers(self):
        # List containers and print as a table
        containers = self.client.containers.list()
        if not containers:
            print("No containers found.")
            return
        table = PrettyTable()
        table.field_names = ["#","Container ID", "Name", "Status"]
        for index, container in enumerate(containers, start=1):
            table.add_row([index, container.id, container.name, container.status])
        print(table)

    def get_wireguard_ip(self):
        try:
            container = self.client.containers.get(self.name)
            container_id = container.id

            # Run a command inside the container to get the WireGuard IP
            wg_ip = (
                subprocess.check_output(
                    ["docker", "exec", container_id, "ip", "-o", "-4", "addr", "show", "wg0"]
                )
                .decode("utf-8")
                .split()[3]
                .split("/")[0]
            )

            print("[+]Your ondemand operating system is deployed: ssh "+self.name+"@"+wg_ip)

        except docker.errors.NotFound:
            print(f"Container with name '{self.name}' not found.")
            return None
        except subprocess.CalledProcessError:
            print(f"WireGuard IP not found for container '{self.name}'.")
            return None