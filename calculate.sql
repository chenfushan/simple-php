create database calculate;

create table rate(
	symbol varchar(30) not null primary key,
	price float not null comment '汇率',
	bid float not null,
	ask float not null,
	time_stamp int not null comment '汇率时间戳',
	modify_time datetime not null,
	create_time datetime not null
)