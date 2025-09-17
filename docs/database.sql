DROP DATABASE hotel;
CREATE DATABASE hotel;
use hotel;

create table user(
id_user int PRIMARY KEY AUTO_INCREMENT,
role ENUM ("admin", "viewer","secretary") default "viewer",
name VARCHAR(50) not null,
mail VARCHAR(50) unique not null 
);

create table hotel(
id_hotel  int PRIMARY KEY AUTO_INCREMENT,
name varchar(50) not null,
address varchar(50) not null,
city  varchar(50) not null,
country varchar(50) not null,
number varchar(15) not null
);

create table type_room (
id_type int PRIMARY KEY AUTO_INCREMENT,
room_name varchar(50) not null,
service int,
bed int
);

create table room (
id_room int PRIMARY KEY AUTO_INCREMENT,
type_id int,
variant int default 0,
price double,
user_id int,
hotel_id int,
FOREIGN KEY (type_id) REFERENCES type(id_type),
FOREIGN KEY (user_id) REFERENCES user(id_user),
FOREIGN KEY (hotel_id) REFERENCES hotel(id_hotel)
);

FOREIGN KEY (hotel_id) REFERENCES hotel(id_hotel),
FOREIGN KEY (room_id) REFERENCES room(id_room)

);