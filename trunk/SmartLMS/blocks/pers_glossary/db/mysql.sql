CREATE TABLE prefix_pgq_langs
(
	id bigint(10) not null primary key auto_increment,
	userid bigint(10) not null references prefix_user,
	name varchar(64) not null,
	unique(userid,name)
);

CREATE TABLE prefix_pgq_terms
(
	id bigint(10) not null primary key auto_increment,
	userid bigint(10) not null references prefix_user,
	source_lang bigint(10) not null references prefix_pgq_langs,
	target_lang bigint(10) not null references prefix_pgq_langs,
	sl_value varchar(64) not null,
	tl_value varchar(64) not null,
	sl_notes text not null,
	tl_notes text not null,
	unique (source_lang,target_lang,sl_value)
);
