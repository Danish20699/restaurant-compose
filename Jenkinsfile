pipeline {
    agent any

    environment {
        IMAGE_NAME = 'danishdopsa/restaurant-app'
        STAGING_IP = '192.168.56.101'
        PROD_IP    = '192.168.56.102'
    }

    stages {
        stage('1. Checkout Code') {
            steps {
                echo 'Checking out code from GitHub...'
                checkout scm
            }
        }

        stage('2. Build Docker Image') {
            steps {
                echo "Building Docker image: ${IMAGE_NAME}:v${BUILD_NUMBER}"
                sh "docker build -t ${IMAGE_NAME}:v${BUILD_NUMBER} ."
                sh "docker tag ${IMAGE_NAME}:v${BUILD_NUMBER} ${IMAGE_NAME}:latest"
            }
        }

        stage('3. Push to Docker Hub') {
            steps {
                echo 'Authenticating and pushing image to Docker Hub...'
                withCredentials([usernamePassword(credentialsId: 'dockerhub-creds', usernameVariable: 'DH_USER', passwordVariable: 'DH_PASS')]) {
                    sh 'echo "$DH_PASS" | docker login -u "$DH_USER" --password-stdin'
                    sh "docker push ${IMAGE_NAME}:v${BUILD_NUMBER}"
                    sh "docker push ${IMAGE_NAME}:latest"
                }
            }
        }

        stage('4. Deploy to Staging') {
            steps {
                echo "Deploying to Staging VM (${STAGING_IP})..."
                withCredentials([sshUserPrivateKey(credentialsId: 'verjenkins-ssh', keyFileVariable: 'SSH_KEY')]) {
                    sh """
                        ssh -i \$SSH_KEY -o StrictHostKeyChecking=no verjenkins@${STAGING_IP} "mkdir -p ~/app/db"
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no docker-compose.yml verjenkins@${STAGING_IP}:~/app/
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no db/init.sql verjenkins@${STAGING_IP}:~/app/db/
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no .env.example verjenkins@${STAGING_IP}:~/app/.env
                        ssh -i \$SSH_KEY -o StrictHostKeyChecking=no verjenkins@${STAGING_IP} "cd ~/app && DOCKER_IMAGE=${IMAGE_NAME}:v${BUILD_NUMBER} docker compose pull && DOCKER_IMAGE=${IMAGE_NAME}:v${BUILD_NUMBER} docker compose up -d"
                    """
                }
                echo "✅ Staging deployed! Accessible at http://${STAGING_IP}:8080"
            }
        }

        stage('5. Manual Approval Gate') {
            steps {
                echo "Waiting for team review of Staging..."
                input message: "Staging is live at http://${STAGING_IP}:8080! Ready to promote to Production?",
                      ok: "Deploy to Production"
            }
        }

        stage('6. Deploy to Production') {
            steps {
                echo "Promoting to Production VM (${PROD_IP})..."
                withCredentials([sshUserPrivateKey(credentialsId: 'verjenkins-ssh', keyFileVariable: 'SSH_KEY')]) {
                    sh """
                        ssh -i \$SSH_KEY -o StrictHostKeyChecking=no verjenkins@${PROD_IP} "mkdir -p ~/app/db"
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no docker-compose.yml verjenkins@${PROD_IP}:~/app/
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no db/init.sql verjenkins@${PROD_IP}:~/app/db/
                        scp -i \$SSH_KEY -o StrictHostKeyChecking=no .env.example verjenkins@${PROD_IP}:~/app/.env
                        ssh -i \$SSH_KEY -o StrictHostKeyChecking=no verjenkins@${PROD_IP} "cd ~/app && DOCKER_IMAGE=${IMAGE_NAME}:v${BUILD_NUMBER} docker compose pull && DOCKER_IMAGE=${IMAGE_NAME}:v${BUILD_NUMBER} docker compose up -d"
                    """
                }
                echo "🚀 LIVE IN PRODUCTION! Accessible at http://${PROD_IP}:8080"
            }
        }
    }

    post {
        success {
            echo "🎉 Complete CI/CD Pipeline executed successfully!"
        }
        failure {
            echo "❌ Pipeline failed! Check console output for logs."
        }
    }
}
