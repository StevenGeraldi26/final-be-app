# Project Setup Instructions

Follow the steps below to set up the project after cloning the repository:

1. **Clone the Repository**  
    Clone the repository to your local machine using the following command:
    ```bash
    git clone <repository-url>
    ```

2. **Install Dependencies**  
    Navigate to the project directory and install the required dependencies using Composer:
    ```bash
    composer install
    ```

3. **Set Up Environment File**  
    Duplicate the `env.example` file and rename it to `.env`:
    ```bash
    cp env.example .env
    ```

4. **Generate Application Key**  
    Generate the application key using the Artisan command:
    ```bash
    php artisan key:generate
    ```

Your project is now set up and ready to use. Follow additional instructions in the documentation for further configuration or usage.
