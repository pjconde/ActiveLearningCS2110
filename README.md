# Active Learning
Active learning tool for Georgia Tech CS2110

Created by JSON's Deli
   - Katherine Cabezas - kcabezas@gatech.edu 
   - Pablojose Conde - pjconde@gatech.edu 
   - Tejasvi Devarakonda - tdevarakonda3@gatech.edu 
   - Pearl Ruparel - pruparel3@gatech.edu 
   - Alexandra Thompson - athompson77@gatech.edu 
   - Kaiwen Wang - kwang329@gatech.edu
   
## Release Notes version 1
### NEW FEATURES
   - First Alpha Release
   - UI Overhaul 
### BUG FIXES
   - Student and Professor interaction now available through Server Sent Events
### KNOWN BUGS
   - When running locally, student and professor questions error when page is refreshed
   - Timer does not shut off student answers from being input
   - Georgia Tech CAS Integration not functional
   - Georgia Tech virtual machine hosting not completed
   - Professor can only log in with one username/ professor cannot register

## Instalation Guide
### PRE-REQUISITES
   - You must have a web browser that supports core features of HTML5 and Bootstrap.
### DEPENDENCIES
   - If on Mac, download and install Bitnami MAMP
   - If on Windows, download and install Bitnami WAMP
   - If on Linux, download and install Bitnami LAMP
### DOWNLOAD
   - github.com/pjconde/ActiveLearningCS2110
### BUILD
   - No build necessary for this app
### INSTALLATION
   - Navigate to where MAMP/LAMP/WAMP installed using your folder navigator of choice
   - Navigate to the apache2/htdocs file directory
   - Copy the ActiceLearning2110 folder into htdocs
### RUNNING APPLICATION
   - Launch the MAMP/WAMP/LAMP configurator
   - Click on Manage Services
   - Select Apache Web Server and click on Start
   - Open your browser of choice 
   - Enter 127.0.0.1/ActiveLearning2110/ as a web address
   - If you want to log in as a professor use username: professor passoword: gatech
   - If you want to log in as a student, your username must have been added by a professor, and you must register first
### TROUBLESHOOTING
   - If browser not showing the web page be sure the Apache Web Server is running
   - If database errors occur, be sure to be on a Georgia Tech network as the database denies access to IP addresses outside Georgia Tech
   - If database error persists contact pjconde@gatech.edu as database might have been shut off on personal Amazon Web Services account



