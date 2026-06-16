# mvc-2025

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mats-jonis/mvc-2025/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/mats-jonis/mvc-2025/?branch=main)

![symfony-twig-template](https://github.com/user-attachments/assets/f16f1ac6-707c-4ad3-b732-61b3a318efb9)

A web application using Twig for templating and Webpack for asset management. This project serves as a base structure for future development with a MVC architecture.

Techstack:

- Symfony 7.2.5 framework
- Twig templating
- Webpack Encore
- MVC structure

Installation:

1. Clone the repository:
   git clone https://github.com/mats-jonis/mvc-2025.git
   cd mvc-2025

2. Install dependencies:

   composer install
   npm install

3. Configure environment variables:

   Copy the .env file and update your database credentials:

   cp .env .env.local

4. Create the database

   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate

5. Build assets:

npm run dev

7. Run the server:

symfony server:start

Author:
Created by mats-jonis
