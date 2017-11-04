drop database if exists jobApplication;

create database jobApplication;

use jobApplication;

create table user(id int primary key auto_increment, name varchar(200) not null, password varchar(200) not null);

create table userDownloads(id int primary key auto_increment, folder varchar(500) not null, userId int not null, downloadTime datetime not null, foreign key(userId) references user(id));

create table userAddress(userId int primary key, gender varchar(1) not null, degree varchar(10) not null, firstName varchar(50) not null, lastName varchar(50) not null, street varchar(50) not null, postCode varchar(20) not null, city varchar(50) not null, email varchar(200) not null, mobilePhone varchar(30) not null, phone varchar(30) not null, birthday varchar(10) not null, birthplace varchar(50) not null, maritalStatus varchar(20) not null, foreign key(userId) references user(id));

create table jobApplicationTemplate(id int primary key auto_increment, userId int not null, templateName varchar(100) not null, userAppliesAs varchar(200) not null, emailSubject varchar(100) not null, emailBody varchar(1000) not null, odtFile varchar(200), foreign key(userId) references user(id)); 

create table jobApplicationPdfAppendix(id int primary key auto_increment, name varchar(50) not null, jobApplicationTemplateId int not null, pdfFile varchar(200) not null, foreign key(jobApplicationTemplateId) references jobApplicationTemplate(id));

create table employer(id int primary key auto_increment, userId int not null, companyName varchar(100) not null, street varchar(50) not null, postCode varchar(20) not null, city varchar(50) not null, gender varchar(1) not null, degree varchar(10) not null, firstName varchar(50) not null, lastName varchar(50) not null, email varchar(200) not null, mobilePhone varchar(30) not null, phone varchar(30) not null, foreign key(userId) references user(id));

create table jobApplication(id int primary key auto_increment, userId int not null, employerId int not null, jobApplicationTemplateId int not null, foreign key(jobApplicationTemplateId) references jobApplicationTemplate(id), foreign key(employerId) references employer(id), foreign key(userId) references user(id));

create table jobApplicationStatusValue(id int primary key, status varchar(50));

create table jobApplicationStatus(id int primary key auto_increment, jobApplicationId int, statusChangedOn date, dueOn datetime, statusValueId int, statusMessage varchar(200), foreign key(jobApplicationId) references jobApplication(id), foreign key(statusValueId) references jobApplicationStatusValue(id));


create table jobCenterContract(id int primary key auto_increment, userId int not null, repeatEvery int not null, jobApplicationCount int not null, expireDate date not null, foreign key(userId) references user(id));

insert into user(id, name, password) values(1, "rene", "1234");
insert into user(id, name, password) values(2, "helmut", "HelmutGoerke1963");
insert into userAddress(userId, gender, degree, firstName, lastName, street, postCode, city, email, mobilePhone, phone, birthday, birthplace, maritalStatus) values(1, "m", "", "René", "Ederer", "Raabstr. 24A", "90429", "Nürnberg", "rene.ederer.nbg@gmail.com", "01520 2723494", "", "19.07.1982", "Nürnberg", "ledig");

insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile) values(1, "Mein Template", "Fachinformatiker für Anwendungsentwicklung", "Bewerbung als Fachinformatiker für Anwendungsentwicklung", "Sehr $geehrter $chefAnrede $chefNachname,\n\nanbei schicke ich Ihnen meine Bewerbungsunterlagen.\nDas Jobcenter kann während der Einarbeitungszeit (auch mehrere Monate) bis zu 50% der Gehaltskosten übernehmen.\nMeine Sachbearbeiterin Frau Götz (jobcenter-nuernberg-stadt.mitte-ag-team@jobcenter-ge.de) gibt Ihnen gerne nähere Auskunft.\nÜber eine Einladung zu einem Bewerbungsgespräch würder ich mich sehr freuen.\n\nMit freundlichen Grüßen\n\n\n$meinVorname $meinNachname\n$meineStrasse\n$meinePlz $meineStadt\n$meineMobilnr", 'C:/Users/rene/Desktop/bewerbung_neu.odt');
insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile) values(1, "Mein Template ohne Anhang", "Fachinformatiker für Anwendungsentwicklung", "Bewerbung als Fachinformatiker für Anwendungsentwicklung", "Sehr $geehrter $chefAnrede $chefNachname,\n\nanbei schicke ich Ihnen meine Bewerbungsunterlagen.\nDas Jobcenter kann während der Einarbeitungszeit (auch mehrere Monate) bis zu 50% der Gehaltskosten übernehmen.\nMeine Sachbearbeiterin Frau Götz (jobcenter-nuernberg-stadt.mitte-ag-team@jobcenter-ge.de) gibt Ihnen gerne nähere Auskunft.\nÜber eine Einladung zu einem Bewerbungsgespräch würder ich mich sehr freuen.\n\nMit freundlichen Grüßen\n\n\n$meinVorname $meinNachname\n$meineStrasse\n$meinePlz $meineStadt\n$meineMobilnr", 'C:/Users/rene/Desktop/bewerbung_neu.odt');

insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('ihkZeugnis', 1, 'c:/users/rene/Downloads/ihk_zeugnis_small.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('segitzZeugnis', 1, 'c:/users/rene/Downloads/segitz_zeugnis_small.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('kmkZeugnis', 1, 'c:/users/rene/Downloads/kmk_zeugnis_small.pdf');
insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values ('labenwolfZeugnis', 1, 'c:/users/rene/Downloads/labenwolf_zeugnis_small.pdf');

insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "BJC BEST JOB IT SERVICES GmbH", "Frau", "", "Katrin", "Thoms", "Alte Rabenstraße 32", "20148", "Hamburg", "Katrin.Thoms@bjc-its.de", "+49 (40) 5 14 00 7180", "");
insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "Deutsche Anwaltshotline AG", "Herr", "", "Jonas", "Zimmermann", "Am Plärrer 7", "90443", "Nürnberg", "mail@deutsche-anwaltshotline.de", "+49 911 3765690", "");
insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "ANG.-Punkt und gut! GmbH", "Frau", "", "Jaqueline", "Strauß", "Südwestpark 37-41", "90449", "Nürnberg", "bewerbung@ang.de", "+49 911 525700", "+49 1778876348");
insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "iQ-Bewerbermanagement", "Frau", "", "Nele", "Sommerfeld", "Obernstr. 111", "28832", "Achim bei Bremen", "nele.sommerfeld@iq-bewerbermanagement.de", "+49 40 6003852232", "");
insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "engineering people GmbH", "Herr", "", "Haluk", "Acar", "Südwestpark 60", "90449", "Nürnberg", "haluk.acar@engineering-people.de", "+49 911 239560316", "");
insert into employer(userId, companyName, gender, degree, firstName, lastName, street, postCode, city, email, phone, mobilePhone) values(1, "BFI Informationssysteme GmbH", "Herr", "", "Michael", "Schlund", "Ötterichweg 7", "90411", "Nürnberg", "Michael.Schlund@bfi-info.de", "0911 9457668", "");

insert into jobApplicationStatusValue(id, status) values(1, "Waiting for reply after sending job application");
insert into jobApplicationStatusValue(id, status) values(2, "Appointment for job interview");
insert into jobApplicationStatusValue(id, status) values(3, "Job application rejected without an interview");
insert into jobApplicationStatusValue(id, status) values(4, "Waiting for reply after job interview");
insert into jobApplicationStatusValue(id, status) values(5, "Job application rejected after interview");
insert into jobApplicationStatusValue(id, status) values(6, "Job application accepted after interview");

insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 1, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 2, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 3, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 4, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 5, 1);
insert into jobApplication(userId, employerId, jobApplicationTemplateId) values(1, 6, 1);

insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(1, str_to_date("26.10.2017", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(2, str_to_date("26.10.2017", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(3, str_to_date("26.10.2017", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(4, str_to_date("26.10.2017", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(5, str_to_date("26.01.2017", "%d.%m.%Y"), null, 1, "");
insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
    values(6, str_to_date("26.10.2017", "%d.%m.%Y"), null, 1, "Forwarded by Ms Götz");


/*insert into jobCenterContract(id, userId, repeatOn, jobApplicationCount, expireDate) values(1, 1, , 6, curdate())*/











