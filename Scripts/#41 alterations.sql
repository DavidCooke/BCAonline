alter table users add column email_status char(2) null after email;
alter table user_audits add column email_status char(2) null after email;


DROP TRIGGER UsersAfterDelete;
DROP TRIGGER UsersAfterInsert;
DROP TRIGGER UsersAfterUpdate;

UPDATE users SET email_status = 'OK' WHERE IFNULL(email, '') != '';
UPDATE user_audits SET email_status = 'OK' WHERE IFNULL(email, '') != '';


DELIMITER $$
CREATE TRIGGER `UsersAfterDelete` AFTER DELETE ON `users` FOR EACH ROW BEGIN
        insert into
            user_audits (audit_datetime, audit_user, audit_type, user_id, username, password, active, 
            email, email_status, forename, surname, organisation, short_name, position,
            bca_status, bca_no, class, class_code, insurance_status, date_of_expiry, address1, address2,
            address3, town, county, postcode, country, telephone, website, gender, year_of_birth, address_ok, allow_club_updates,
            admin_email_ok, bca_email_ok, bcra_email_ok, bcra_member, ccc_member, cncc_member, cscc_member, dca_member, dcuc_member,
            forename2, surname2, bca_no2, insurance_status2,
            class_code2, roles, same_person, created, modified)
        values
            (now(), user(), 'D', old.id, old.username, old.password, old.active, 
            old.email, old.email_status, old.forename, old.surname, old.organisation, old.short_name, old.position,
            old.bca_status, old.bca_no, old.class, old.class_code, old.insurance_status, old.date_of_expiry, old.address1, old.address2,
            old.address3, old.town, old.county, old.postcode, old.country, old.telephone, old.website, old.gender, old.year_of_birth, old.address_ok, old.allow_club_updates,
            old.admin_email_ok, old.bca_email_ok, old.bcra_email_ok, old.bcra_member, old.ccc_member, old.cncc_member, old.cscc_member, old.dca_member, old.dcuc_member,
            old.forename2, old.surname2, old.bca_no2, old.insurance_status2,
            old.class_code2, old.roles, old.same_person, old.created, old.modified);
     END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UsersAfterInsert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
        insert into
            user_audits (audit_datetime, audit_user, audit_type, user_id, username, password, active, 
            email, email_status, forename, surname, organisation, short_name, position,
            bca_status, bca_no, class, class_code, insurance_status, date_of_expiry, address1, address2,
            address3, town, county, postcode, country, telephone, website, gender, year_of_birth, address_ok, allow_club_updates,
            admin_email_ok, bca_email_ok, bcra_email_ok, bcra_member, ccc_member, cncc_member, cscc_member, dca_member, dcuc_member,
            forename2, surname2, bca_no2, insurance_status2,
            class_code2, roles, same_person, created, modified)
        values
            (now(), user(), 'I',  new.id, new.username, new.password, new.active, 
            new.email, new.email_status, new.forename, new.surname, new.organisation, new.short_name, new.position,
            new.bca_status, new.bca_no, new.class, new.class_code, new.insurance_status, new.date_of_expiry, new.address1, new.address2,
            new.address3, new.town, new.county, new.postcode, new.country, new.telephone, new.website, new.gender, new.year_of_birth, new.address_ok, new.allow_club_updates,
            new.admin_email_ok, new.bca_email_ok, new.bcra_email_ok, new.bcra_member, new.ccc_member, new.cncc_member, new.cscc_member, new.dca_member, new.dcuc_member,
            new.forename2, new.surname2, new.bca_no2, new.insurance_status2,
            new.class_code2, new.roles, new.same_person, new.created, new.modified);
     END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UsersAfterUpdate` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
        insert into
            user_audits (audit_datetime, audit_user, audit_type, user_id, username, password, active, 
            email, email_status, forename, surname, organisation, short_name, position,
            bca_status, bca_no, class, class_code, insurance_status, date_of_expiry, address1, address2,
            address3, town, county, postcode, country, telephone, website, gender, year_of_birth, address_ok, allow_club_updates,
            admin_email_ok, bca_email_ok, bcra_email_ok, bcra_member, ccc_member, cncc_member, cscc_member, dca_member, dcuc_member,
            forename2, surname2, bca_no2, insurance_status2,
            class_code2, roles, same_person, created, modified)
        values
            (now(), user(), 'U',  new.id, new.username, new.password, new.active, 
            new.email, new.email_status, new.forename, new.surname, new.organisation, new.short_name, new.position,
            new.bca_status, new.bca_no, new.class, new.class_code, new.insurance_status, new.date_of_expiry, new.address1, new.address2,
            new.address3, new.town, new.county, new.postcode, new.country, new.telephone, new.website, new.gender, new.year_of_birth, new.address_ok, new.allow_club_updates,
            new.admin_email_ok, new.bca_email_ok, new.bcra_email_ok, new.bcra_member, new.ccc_member, new.cncc_member, new.cscc_member, new.dca_member, new.dcuc_member,
            new.forename2, new.surname2, new.bca_no2, new.insurance_status2,
            new.class_code2, new.roles, new.same_person, new.created, new.modified);
     END
$$
DELIMITER ;