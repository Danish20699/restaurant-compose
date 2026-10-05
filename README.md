# 🍽️ Gourmet Bistro — LAPP Stack Multi-Tier Application

A fully containerized **LAPP (Linux, Apache, PostgreSQL, PHP)** stack with automated CI/CD pipeline deployment across Staging and Production environments.

## 🏛️ Architecture & Environments
- **CI/CD Controller:** Jenkins Pipeline
- **Image Registry:** Docker Hub
- **Staging Environment:** 192.168.56.101:8080
- **Production Environment:** 192.168.56.102:8080

## 🚀 Pipeline Flow
1. Developer Git Push to GitHub
2. Jenkins triggers automated pipeline
3. Build custom Docker image
4. Push image to Docker Hub
5. Deploy to Staging VM via SSH (verjenkins)
6. Manual Approval Gate
7. Deploy to Production VM via SSH (verjenkins)
