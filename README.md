# Minecraft Modpack Manager

A Minecraft-inspired modpack manager API designed to work with [BertobrLauncher](https://github.com/brutalzinn/bertobrlauncher), a custom Minecraft launcher.


## Table of Contents
- [Minecraft Modpack Manager](#minecraft-modpack-manager)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [Prerequisites](#prerequisites)
    - [Installing Docker](#installing-docker)
    - [Installing Docker Compose](#installing-docker-compose)
  - [Setup](#setup)
  - [Running the Project](#running-the-project)
  - [Stopping the Project](#stopping-the-project)
  - [Screens](#screens)

## Introduction

This project is a modpack manager API for Minecraft, designed to be used with [BertobrLauncher](https://github.com/brutalzinn/bertobrlauncher). It allows you to create, manage, and version modpacks through a web interface.

## Prerequisites

To run this project, you need to have Docker and Docker Compose installed on your machine.

### Installing Docker

1. **For Windows and macOS:**
   - Download Docker Desktop from the [Docker website](https://www.docker.com/products/docker-desktop) and follow the installation instructions.
   
2. **For Linux:**
   - Use the following commands to install Docker:
     ```bash
     sudo apt-get update
     sudo apt-get install -y docker.io
     sudo systemctl start docker
     sudo systemctl enable docker
     ```
   
### Installing Docker Compose

1. **For Windows and macOS:**
   - Docker Compose is included with Docker Desktop, so no additional installation is required.
   
2. **For Linux:**
   - Install Docker Compose by running the following command:
     ```bash
     sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
     sudo chmod +x /usr/local/bin/docker-compose
     ```

## Setup

1. **Clone the repository**:
    ```bash
    git clone https://github.com/your-username/minecraft-modpack-manager.git
    cd minecraft-modpack-manager
    ```

## Running the Project

To start the project using Docker Compose, follow these steps:

1. **Build and run the Docker containers**:
    ```bash
    docker-compose up --build
    ```

    This command will build the Docker images and start the containers defined in the `docker-compose.yml` file.

2. **Access the API**:

    Once the containers are up and running, you can access the API by navigating to [http://localhost:3000](http://localhost:3000) in your web browser.

## Stopping the Project

To stop the running Docker containers, press `Ctrl + C` in the terminal where `docker-compose up` is running, or use the following command in a separate terminal:

```bash
docker-compose down
```

## Screens 

<img src="docs/home.png">

<img src="docs/edit.png">


