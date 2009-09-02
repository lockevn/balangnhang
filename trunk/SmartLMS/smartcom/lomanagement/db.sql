create table mdl_lo (
id bigint(10) Primary key not null auto_increment,
category bigint(10) not null default 0,
instance bigint(10) not null,
lotype varchar(255) not null
);