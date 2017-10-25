drop database if exists jobApplication;

create database jobApplication;

use jobApplication;

create table user(id int primary key auto_increment, name varchar(200) not null, password varchar(200) not null);

create table userDownloads(id int primary key auto_increment, folder varchar(500) not null, userId int not null, downloadTime datetime not null, foreign key(userId) references user(id));

create table userAddress(userId int primary key, firstName varchar(50) not null, lastName varchar(50) not null, title varchar(10) not null, salutation varchar(20) not null, street varchar(50) not null, postCode varchar(20) not null, city varchar(50) not null, email varchar(200) not null, mobilePhone varchar(30) not null, phone varchar(30) not null, birthday varchar(10) not null, birthplace varchar(50) not null, maritalStatus varchar(20) not null, foreign key(userId) references user(id));

create table jobApplicationTemplate(id int primary key auto_increment, userId int not null, templateName varchar(100) not null, userAppliesAs varchar(200) not null, emailSubject varchar(100) not null, emailBody varchar(1000) not null, odtFile varchar(200), foreign key(userId) references user(id)); 

create table jobApplicationPdfAppendix(id int primary key auto_increment, name varchar(50) not null, jobApplicationTemplateId int not null, pdfFile varchar(200) not null, foreign key(jobApplicationTemplateId) references jobApplicationTemplate(id));

create table employer(id int primary key auto_increment, userId int not null, companyName varchar(100) not null, street varchar(50) not null, postCode varchar(20) not null, city varchar(50) not null, title varchar(10) not null, salutation varchar(20) not null, firstName varchar(50) not null, lastName varchar(50) not null, email varchar(200) not null, mobilePhone varchar(30) not null, phone varchar(30) not null, foreign key(userId) references user(id));

create table jobApplication(id int primary key auto_increment, userId int not null, employerId int not null, jobApplicationTemplateId int not null, foreign key(jobApplicationTemplateId) references jobApplicationTemplate(id), foreign key(employerId) references employer(id), foreign key(userId) references user(id));

create table jobApplicationStatusValue(id int primary key, status varchar(50));

create table jobApplicationStatus(id int primary key auto_increment, jobApplicationId int, statusChangedOn date, dueOn datetime, statusValueId int, statusMessage varchar(200), foreign key(jobApplicationId) references jobApplication(id), foreign key(statusValueId) references jobApplicationStatusValue(id));


create table jobCenterContract(id int primary key auto_increment, userId int not null, repeatEvery varchar(50) not null, jobApplicationCount int not null, expireDate date not null, foreign key(userId) references user(id));

insert into user(id, name, password) values(1, "rene", "1234");
insert into user(id, name, password) values(2, "helmut", "HelmutGoerke1963");
insert into userAddress(userId, firstName, lastName, title, salutation, street, postCode, city, email, mobilePhone, phone, birthday, birthplace, maritalStatus) values(1, "René", "Ederer", "", "Herr", "Raabstr. 24A", "90429", "Nürnberg", "rene.ederer.nbg@gmail.com", "01520 2723494", "", "19.07.1982", "Nürnberg", "ledig");

insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile) values(1, "Mein Template", "Fachinformatiker für Anwendungsentwicklung", "Bewerbung als Fachinformatiker für Anwendungsentwicklung", "Sehr $geehrter $chefAnrede $chefNachname,\nanbei schicke ich Ihnen meine Bewerbungsunterlagen.\nÜber eine Einladung zu einem Bewerbungsgespräch würder ich mich sehr freuen.\n\nMit freundlichen Grüßen\n$meinVorname $meinNachname\n$meineStrasse\n$meinePlz $meineStadt\n$meineMobilnr", 'C:/Users/rene/Desktop/bewerbung_neu.odt');
insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile) values(1, "Mein Template ohne Anhänge", "Fachinformatiker für Anwendungsentwicklung", "Bewerbung als Fachinformatiker für Anwendungsentwicklung", "Sehr $geehrter $chefAnrede $chefTitel $chefNachname,\n\nanbei schicke ich Ihnen meine Bewerbungsunterlagen.\nÜber eine Einladung zu einem Bewerbungsgespräch würde ich mich sehr freuen.\nMit freundlichen Grüßen\n\n$meinVorname $meinNachname\n$meineStrasse\n$meinePlz $meineStadt\n$meineMobilnr", 'C:/Users/rene/Desktop/bewerbung_neu.odt');

insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('ihkZeugnis', 1, 'C:/UniserverZ/www/bewerbung/ihkZeugnis.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('segitzZeugnis', 1, 'C:/Users/rene/bewerbung/segitzZeugnis.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('kmkZeugnis', 1, 'C:/Users/rene/bewerbung/kmkZeugnis.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('labenwolfZeugnis', 1, 'C:/Users/rene/bewerbung/labenwolfZeugnis.pdf');

insert into employer(userId, companyName, firstName, lastName, title, salutation, street, postCode, city, email, phone, mobilePhone) values(1, "IBF GmbH", "Robert", "Schlund", "", "Herr", "Oettinger Str. 11", "90411", "Nuernberg", "rene.ederer.nbg@gmail.com", "0911 123456", "0171 7471729");
insert into employer(userId, companyName, firstName, lastName, title, salutation, street, postCode, city, email, phone, mobilePhone) values(1, "Siemens", "Hans", "Meier", "Dr.", "Herr", "Melanchtonstr. 2b", "90765", "Fuerth", "rene.ederer.nbg@gmail.com", "040 938102", "01502 9420494");
insert into employer(userId, companyName, firstName, lastName, title, salutation, street, postCode, city, email, phone, mobilePhone) values(1, "BJC BEST JOB IT SERVICES GmbH", "Felix", "Preukschat", "", "Herr", "Alte Rabenstraße 32", "20148", "Hamburg", "rene.ederer.nbg@gmail.com", "+49 (40) 5 14 00 72 40", "");

insert into jobApplicationStatusValue(id, status) values(1, "Waiting for reply after sending job application");
insert into jobApplicationStatusValue(id, status) values(2, "Appointment for job interview");
insert into jobApplicationStatusValue(id, status) values(3, "Job application rejected without an interview");
insert into jobApplicationStatusValue(id, status) values(4, "Waiting for reply after job interview");
insert into jobApplicationStatusValue(id, status) values(5, "Job application rejected after interview");
insert into jobApplicationStatusValue(id, status) values(6, "Job application accepted after interview");

insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 1, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 2, 1);

insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(1, curdate(), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(2, str_to_date("01.01.2004", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(2, str_to_date("10.01.2004", "%d.%m.%Y"), null, 2, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(2, str_to_date("20.01.2004", "%d.%m.%Y"), null, 4, "");


insert into jobCenterContract(id, userId, repeatEvery, jobApplicationCount, expireDate) values(1, 1, "1 Month", 6, curdate())











