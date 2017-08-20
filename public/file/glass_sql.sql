create table goods
(
	id int(10) unsigned auto_increment
		primary key,
	goods_name varchar(64) not null comment '商品名称',
	category_id int not null comment '分类id',
	time_limit tinyint(2) default '7' null comment '工期(单位：天)',
	created_at timestamp null comment '创建时间',
	updated_at timestamp null comment '修改时间',
	browser_num int default '0' null comment '浏览量（该字段不建议放在此表，操作过于频繁，压力太大）',
	buy_num int default '0' null comment '购买量',
	goods_status tinyint(1) default '1' not null comment '商品状态（1：上架，2：下架）',
	deleted_at timestamp null comment '删除时间',
	unit_price int not null comment '商品单价（按平方计算）（单位：分）',
	constraint goods_name_UNIQUE
		unique (goods_name)
)
comment '商品表'
;

create table goods_category
(
	id int(10) unsigned auto_increment
		primary key,
	parent_id int default '0' not null comment '父级ID',
	name varchar(45) not null comment '名称',
	created_at timestamp null,
	updated_at timestamp null
)
comment '商品分类表'
;

create table goods_details
(
	id int auto_increment
		primary key,
	goods_id int not null comment '商品ID',
	details text not null comment '商品详情信息（富文本）',
	constraint goods_details_goods_id_uindex
		unique (goods_id)
)
comment '商品详情表'
;

create table goods_img
(
	id int auto_increment
		primary key,
	goods_id int not null,
	img_url varchar(256) not null
)
comment '商品图片表'
;

create index goods_img__goods_id
	on goods_img (goods_id)
;

create table goods_share_info
(
	id int auto_increment
		primary key,
	goods_id int not null comment '商品ID',
	share_title varchar(64) not null comment '分享标题',
	share_img varchar(256) not null comment '分享图片路径',
	share_content varchar(256) not null comment '分享内容',
	constraint goods_share_info_goods_id_uindex
		unique (goods_id)
)
comment '商品分享信息表'
;

create table goods_show_category
(
	id int auto_increment
		primary key,
	name varchar(45) not null comment '展示分类名称',
	created_at timestamp null,
	updated_at timestamp null,
	dateled_at timestamp null,
	constraint name_UNIQUE
		unique (name)
)
comment '商品展示分类'
;

create table goods_showcate_rela
(
	id int auto_increment
		primary key,
	show_category_id varchar(45) not null,
	goods_id varchar(45) not null
)
comment '商品-展示分类关系表'
;

create index `show_category_id,goods_id`
	on goods_showcate_rela (show_category_id, goods_id)
;

