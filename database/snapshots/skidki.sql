--
-- PostgreSQL database dump
--

-- Dumped from database version 11.6 (Ubuntu 11.6-1.pgdg16.04+1)
-- Dumped by pg_dump version 12.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

--
-- Name: achievements; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE achievements (
    id integer NOT NULL,
    title character varying(255) DEFAULT ''::character varying NOT NULL,
    description character varying(1000) DEFAULT ''::character varying NOT NULL,
    ach_image_url character varying(1000) NOT NULL,
    trigger_type integer NOT NULL,
    trigger_value integer DEFAULT 0 NOT NULL,
    prize_description character varying(1000) NOT NULL,
    prize_image_url character varying(1000) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT false NOT NULL
);


ALTER TABLE achievements OWNER TO vrawpxxujbruhc;

--
-- Name: achievements_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE achievements_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE achievements_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: achievements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE achievements_id_seq OWNED BY achievements.id;


--
-- Name: articles; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE articles (
    id integer NOT NULL,
    url character varying(1000) NOT NULL,
    part integer NOT NULL,
    is_visible boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE articles OWNER TO vrawpxxujbruhc;

--
-- Name: articles_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE articles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE articles_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: articles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE articles_id_seq OWNED BY articles.id;


--
-- Name: cashback_histories; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE cashback_histories (
    id integer NOT NULL,
    money_in_check integer NOT NULL,
    user_phone character varying(255) DEFAULT ''::character varying,
    check_info character varying(255) DEFAULT ''::character varying NOT NULL,
    activated integer DEFAULT 0 NOT NULL,
    employee_id integer NOT NULL,
    company_id integer NOT NULL,
    user_id integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE cashback_histories OWNER TO vrawpxxujbruhc;

--
-- Name: cashback_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE cashback_histories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE cashback_histories_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: cashback_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE cashback_histories_id_seq OWNED BY cashback_histories.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE categories (
    id integer NOT NULL,
    title character varying(255) DEFAULT ''::character varying NOT NULL,
    description character varying(255) DEFAULT ''::character varying,
    image_url character varying(1000) DEFAULT ''::character varying,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer DEFAULT 0 NOT NULL
);


ALTER TABLE categories OWNER TO vrawpxxujbruhc;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE categories_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE categories_id_seq OWNED BY categories.id;


--
-- Name: companies; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE companies (
    id integer NOT NULL,
    title character varying(255) DEFAULT ''::character varying NOT NULL,
    address character varying(500) DEFAULT ''::character varying NOT NULL,
    description character varying(500) DEFAULT ''::character varying NOT NULL,
    phone character varying(255) DEFAULT ''::character varying NOT NULL,
    email character varying(255) DEFAULT ''::character varying NOT NULL,
    bailee character varying(255) DEFAULT ''::character varying NOT NULL,
    logo_url character varying(1000) DEFAULT ''::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer DEFAULT 0 NOT NULL,
    cashback integer DEFAULT 5 NOT NULL,
    telegram_bot_url character varying(255) DEFAULT ''::character varying NOT NULL
);


ALTER TABLE companies OWNER TO vrawpxxujbruhc;

--
-- Name: companies_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE companies_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE companies_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: companies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE companies_id_seq OWNED BY companies.id;


--
-- Name: events; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE events (
    id integer NOT NULL,
    title character varying(255) DEFAULT ''::character varying,
    description character varying(1000) DEFAULT ''::character varying,
    event_image_url character varying(1000),
    start_at timestamp(0) without time zone NOT NULL,
    end_at timestamp(0) without time zone NOT NULL,
    company_id integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer DEFAULT 0 NOT NULL
);


ALTER TABLE events OWNER TO vrawpxxujbruhc;

--
-- Name: events_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE events_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE events_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: events_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE events_id_seq OWNED BY events.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE migrations OWNER TO vrawpxxujbruhc;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE migrations_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE migrations_id_seq OWNED BY migrations.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE password_resets OWNER TO vrawpxxujbruhc;

--
-- Name: prizes; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE prizes (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    description character varying(1000) DEFAULT ''::character varying NOT NULL,
    image_url character varying(1000) NOT NULL,
    summary_activation_count integer DEFAULT 0 NOT NULL,
    current_activation_count integer DEFAULT 0 NOT NULL,
    is_active boolean DEFAULT false NOT NULL,
    company_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE prizes OWNER TO vrawpxxujbruhc;

--
-- Name: prizes_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE prizes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE prizes_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: prizes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE prizes_id_seq OWNED BY prizes.id;


--
-- Name: promocodes; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE promocodes (
    id integer NOT NULL,
    code character varying(255) NOT NULL,
    activated boolean DEFAULT false NOT NULL,
    prize_has_taken boolean DEFAULT false NOT NULL,
    user_id integer,
    prize_id integer,
    company_id integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE promocodes OWNER TO vrawpxxujbruhc;

--
-- Name: promocodes_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE promocodes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE promocodes_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: promocodes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE promocodes_id_seq OWNED BY promocodes.id;


--
-- Name: promotions; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE promotions (
    id integer NOT NULL,
    title character varying(255) DEFAULT ''::character varying,
    description character varying(1000) DEFAULT ''::character varying,
    promo_image_url character varying(1000),
    handler character varying(255),
    start_at timestamp(0) without time zone NOT NULL,
    end_at timestamp(0) without time zone NOT NULL,
    activation_count integer DEFAULT 0 NOT NULL,
    current_activation_count integer DEFAULT 0 NOT NULL,
    location_address character varying(255) DEFAULT ''::character varying,
    location_coords character varying(255) DEFAULT ''::character varying,
    immediately_activate boolean DEFAULT false NOT NULL,
    activation_text character varying(1000) DEFAULT ''::character varying NOT NULL,
    refferal_bonus integer DEFAULT 0,
    company_id integer,
    category_id integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer DEFAULT 0 NOT NULL
);


ALTER TABLE promotions OWNER TO vrawpxxujbruhc;

--
-- Name: promotions_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE promotions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE promotions_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: promotions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE promotions_id_seq OWNED BY promotions.id;


--
-- Name: refferals_histories; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE refferals_histories (
    id integer NOT NULL,
    user_sender_id integer NOT NULL,
    user_recipient_id integer NOT NULL,
    activated boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE refferals_histories OWNER TO vrawpxxujbruhc;

--
-- Name: refferals_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE refferals_histories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE refferals_histories_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: refferals_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE refferals_histories_id_seq OWNED BY refferals_histories.id;


--
-- Name: refferals_payment_histories; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE refferals_payment_histories (
    id integer NOT NULL,
    user_id integer NOT NULL,
    employee_id integer NOT NULL,
    company_id integer,
    value integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE refferals_payment_histories OWNER TO vrawpxxujbruhc;

--
-- Name: refferals_payment_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE refferals_payment_histories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE refferals_payment_histories_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: refferals_payment_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE refferals_payment_histories_id_seq OWNED BY refferals_payment_histories.id;


--
-- Name: stats; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE stats (
    id integer NOT NULL,
    stat_type integer NOT NULL,
    stat_value integer NOT NULL,
    user_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE stats OWNER TO vrawpxxujbruhc;

--
-- Name: stats_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE stats_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE stats_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: stats_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE stats_id_seq OWNED BY stats.id;


--
-- Name: user_has_achievements; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE user_has_achievements (
    id integer NOT NULL,
    activated boolean DEFAULT false NOT NULL,
    user_id integer NOT NULL,
    achievement_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE user_has_achievements OWNER TO vrawpxxujbruhc;

--
-- Name: user_has_achievements_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE user_has_achievements_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_has_achievements_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: user_has_achievements_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE user_has_achievements_id_seq OWNED BY user_has_achievements.id;


--
-- Name: user_has_promos; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE user_has_promos (
    id integer NOT NULL,
    user_id integer NOT NULL,
    promotion_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE user_has_promos OWNER TO vrawpxxujbruhc;

--
-- Name: user_has_promos_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE user_has_promos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_has_promos_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: user_has_promos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE user_has_promos_id_seq OWNED BY user_has_promos.id;


--
-- Name: user_in_companies; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE user_in_companies (
    id integer NOT NULL,
    user_id integer NOT NULL,
    company_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE user_in_companies OWNER TO vrawpxxujbruhc;

--
-- Name: user_in_companies_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE user_in_companies_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_in_companies_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: user_in_companies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE user_in_companies_id_seq OWNED BY user_in_companies.id;


--
-- Name: user_info; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE user_info (
    id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE user_info OWNER TO vrawpxxujbruhc;

--
-- Name: user_info_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE user_info_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_info_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: user_info_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE user_info_id_seq OWNED BY user_info.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE TABLE users (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    fio_from_telegram character varying(255) DEFAULT ''::character varying NOT NULL,
    fio_from_request character varying(255) DEFAULT ''::character varying,
    phone character varying(255),
    avatar_url character varying(1000) DEFAULT ''::character varying,
    address character varying(500),
    sex smallint,
    age smallint,
    birthday character varying(255),
    source character varying(255) DEFAULT ''::character varying NOT NULL,
    telegram_chat_id character varying(255) NOT NULL,
    referrals_count integer DEFAULT 0 NOT NULL,
    referral_bonus_count integer DEFAULT 0 NOT NULL,
    cashback_bonus_count integer DEFAULT 0 NOT NULL,
    is_admin boolean DEFAULT false NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    parent_id integer,
    activated integer DEFAULT 0 NOT NULL,
    current_network_level integer DEFAULT 0 NOT NULL,
    network_cashback_bonus_count double precision DEFAULT '0'::double precision NOT NULL,
    network_friends_count integer DEFAULT 0 NOT NULL
);


ALTER TABLE users OWNER TO vrawpxxujbruhc;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: vrawpxxujbruhc
--

CREATE SEQUENCE users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE users_id_seq OWNER TO vrawpxxujbruhc;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: vrawpxxujbruhc
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: achievements id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY achievements ALTER COLUMN id SET DEFAULT nextval('achievements_id_seq'::regclass);


--
-- Name: articles id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY articles ALTER COLUMN id SET DEFAULT nextval('articles_id_seq'::regclass);


--
-- Name: cashback_histories id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY cashback_histories ALTER COLUMN id SET DEFAULT nextval('cashback_histories_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY categories ALTER COLUMN id SET DEFAULT nextval('categories_id_seq'::regclass);


--
-- Name: companies id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY companies ALTER COLUMN id SET DEFAULT nextval('companies_id_seq'::regclass);


--
-- Name: events id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY events ALTER COLUMN id SET DEFAULT nextval('events_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY migrations ALTER COLUMN id SET DEFAULT nextval('migrations_id_seq'::regclass);


--
-- Name: prizes id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY prizes ALTER COLUMN id SET DEFAULT nextval('prizes_id_seq'::regclass);


--
-- Name: promocodes id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY promocodes ALTER COLUMN id SET DEFAULT nextval('promocodes_id_seq'::regclass);


--
-- Name: promotions id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY promotions ALTER COLUMN id SET DEFAULT nextval('promotions_id_seq'::regclass);


--
-- Name: refferals_histories id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY refferals_histories ALTER COLUMN id SET DEFAULT nextval('refferals_histories_id_seq'::regclass);


--
-- Name: refferals_payment_histories id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY refferals_payment_histories ALTER COLUMN id SET DEFAULT nextval('refferals_payment_histories_id_seq'::regclass);


--
-- Name: stats id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY stats ALTER COLUMN id SET DEFAULT nextval('stats_id_seq'::regclass);


--
-- Name: user_has_achievements id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_achievements ALTER COLUMN id SET DEFAULT nextval('user_has_achievements_id_seq'::regclass);


--
-- Name: user_has_promos id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_promos ALTER COLUMN id SET DEFAULT nextval('user_has_promos_id_seq'::regclass);


--
-- Name: user_in_companies id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_in_companies ALTER COLUMN id SET DEFAULT nextval('user_in_companies_id_seq'::regclass);


--
-- Name: user_info id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_info ALTER COLUMN id SET DEFAULT nextval('user_info_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: achievements; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO achievements VALUES (3, 'Количество активированных рефералов', 'Количество человек, которые воспользовались Вашей реферальной ссылкой или QR-кодом и после запуска бота заполнили необходимые данные в персональной анкете', 'https://sun9-49.userapi.com/c858336/v858336888/130999/_363AcxJizk.jpg', 1, 300, 'Приз 2', 'https://xn----ftbdbcmcpjvdebdh7a.xn--p1ai/thumb/2/PGa_1JGkvfsTz37PRKoxCA/r/d/skidka_2pr.jpg', '2019-12-09 09:37:42', '2020-01-02 14:03:42', 400, true);
INSERT INTO achievements VALUES (2, 'Количество активированных акций и скидок', 'Количество акций и скидок на товары и услуги наших Партнеров, которое Вы приобрели, используя данный бот', 'https://sun9-21.userapi.com/c858336/v858336580/12f0da/sGLWdbpetfk.jpg', 0, 150, 'Приз 1', 'https://st2.depositphotos.com/3554337/12175/i/950/depositphotos_121750348-stock-photo-1-percent-discount-in-red.jpg', '2019-12-09 09:33:44', '2019-12-23 21:07:16', 900, true);
INSERT INTO achievements VALUES (6, 'Количество активаций по ссылке из Facebook', 'Количество человек, которые воспользовались Вашей реферальной ссылкой в Facebook и запустили бота', 'https://sun9-48.userapi.com/c858336/v858336888/130abe/WHnlp8FuJ5o.jpg', 4, 300, 'Приз 5', 'https://a.d-cd.net/7a8a4es-960.jpg', '2019-12-09 09:42:38', '2020-01-02 14:06:11', 800, true);
INSERT INTO achievements VALUES (10, 'Количество активированных достижений', 'Количество достижений, условия которых Вами были выполнены', 'https://sun9-65.userapi.com/c858336/v858336580/12f0f2/xzE-U6Axsiw.jpg', 8, 5, 'Приз 900', 'https://st2.depositphotos.com/3246347/9758/i/950/depositphotos_97587988-stock-photo-discount-9-percent-off-sale.jpg', '2019-12-09 09:53:59', '2019-12-23 21:11:53', 1000, true);
INSERT INTO achievements VALUES (1, 'Mats Sundin', 'fsdfsd', 'https://static.wixstatic.com/media/361aae_b4cf8f2b0d3d46fe852ca52f41dd0a1f~mv2.png', 3, 100, 'sdasd', 'https://static.wixstatic.com/media/361aae_b4cf8f2b0d3d46fe852ca52f41dd0a1f~mv2.png', '2019-12-07 20:26:32', '2019-12-18 18:30:35', 1, false);
INSERT INTO achievements VALUES (9, 'Сумма реферального бонуса', 'Сумма бонуса, который накапливается Вами за приглашение друзей', 'https://sun9-42.userapi.com/c858336/v858336888/130a49/6QEzd5Bjlx0.jpg', 7, 2000, 'Приз 8', 'https://i.pinimg.com/736x/39/af/ea/39afeac4c5e6dd28943d7d302d168c1c--fashion-com.jpg', '2019-12-09 09:52:27', '2020-01-02 14:04:48', 400, true);
INSERT INTO achievements VALUES (5, 'Количество активаций по ссылке из Вконтакте', 'Количество человек, которые воспользовались Вашей реферальной ссылкой в Вконтакте и запустили бота', 'https://sun9-65.userapi.com/c858336/v858336888/130a86/sq8jSFkrfUc.jpg', 3, 300, 'Приз 4', 'https://st2.depositphotos.com/3246347/11639/i/950/depositphotos_116393022-stock-photo-discount-4-percent-off-3d.jpg', '2019-12-09 09:41:07', '2020-01-02 14:05:51', 600, true);
INSERT INTO achievements VALUES (4, 'Сумма начисленного CashBack', 'Сумма CashBack, который был начислен на Вашего бонусный счет при покупке товаров и услуг наших Партнеров', 'https://sun9-18.userapi.com/c857536/v857536821/132f88/n75rbgPiAMo.jpg', 2, 1000, 'Приз 3', 'https://cs11.livemaster.ru/storage/topicavatar/600x450/5d/8e/d4ba78c82dee69fe748a61a5babda1a8f349ci.jpg?h=fMcXbnXADbMrMHuqahpXMw', '2019-12-09 09:39:33', '2019-12-23 19:53:33', 100, true);
INSERT INTO achievements VALUES (11, 'Сумма списанного CashBack', 'Сумма CashBack, который был списан с Вашего бонусного счета при оплате им товаров и услуг наших Партнеров', 'https://sun9-22.userapi.com/c858336/v858336888/130968/uhztlC7l02I.jpg', 9, 1000, 'Приз 10', 'https://mmvstroy.ru/upload/iblock/86a/86ab1ba8b8b35374c2e777c03e4f7ebc.png', '2019-12-09 09:55:20', '2019-12-23 20:03:48', 200, true);
INSERT INTO achievements VALUES (7, 'Количество активаций по ссылке из Instagram', 'Количество человек, которые воспользовались Вашей реферальной ссылкой в Instagram и запустили бота', 'https://sun9-62.userapi.com/c858336/v858336888/130a90/7PPF-pob-Js.jpg', 5, 300, 'Приз 6', 'https://st.depositphotos.com/1682899/3946/i/950/depositphotos_39461371-stock-photo-6-percent-discount.jpg', '2019-12-09 09:45:34', '2020-01-02 14:06:00', 700, true);
INSERT INTO achievements VALUES (8, 'Количество переходов по ссылке/QR-коду', 'Количество человек, которые воспользовались Вашей ссылкой или реферальным QR-кодом и запустили бота', 'https://sun9-13.userapi.com/c858336/v858336888/13098f/zdHiraSct4I.jpg', 6, 500, 'Приз 7', 'http://www.36i7.ru/site.aspx?IID=2644937', '2019-12-09 09:50:22', '2020-01-02 14:03:30', 300, true);


--
-- Data for Name: articles; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO articles VALUES (1, 'https://telegra.ph/Prizy-za-vypolnenie-dostizhenij-12-23', 0, true, '2019-12-23 14:51:51', '2019-12-23 17:18:10');
INSERT INTO articles VALUES (4, 'https://telegra.ph/Potrebitelyam-uslug-12-24', 6, true, '2019-12-23 15:50:38', '2019-12-24 18:08:41');
INSERT INTO articles VALUES (3, 'https://telegra.ph/Usloviya-obnalichivaniya-bonusov-12-17', 2, true, '2019-12-23 15:49:41', '2019-12-24 19:09:45');
INSERT INTO articles VALUES (2, 'https://telegra.ph/Promouteru-12-17', 1, true, '2019-12-23 15:49:11', '2019-12-24 19:33:15');
INSERT INTO articles VALUES (5, 'https://telegra.ph/Rozygrysh-prizov-01-14', 8, true, '2020-01-14 15:52:58', '2020-01-14 15:52:58');


--
-- Data for Name: cashback_histories; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO cashback_histories VALUES (1, 1000, '+380714320661', '12345567890', 1, 6, 3, 2, '2019-12-03 09:47:13', '2019-12-03 09:47:13');
INSERT INTO cashback_histories VALUES (2, 123123, '+380714320669', '12345567890', 1, 6, 1, 10, '2019-12-03 09:47:53', '2019-12-05 05:26:30');
INSERT INTO cashback_histories VALUES (3, 1000, '+380713943435', 'testCheckNum', 1, 6, 1, 15, '2019-12-08 15:15:21', '2019-12-08 15:15:21');
INSERT INTO cashback_histories VALUES (4, 1000, NULL, '11111111', 1, 15, 1, 37, '2019-12-13 17:23:00', '2019-12-13 17:23:00');
INSERT INTO cashback_histories VALUES (5, 500, NULL, '22222222', 1, 15, 2, 37, '2019-12-13 17:28:42', '2019-12-13 17:28:42');
INSERT INTO cashback_histories VALUES (6, 1000, NULL, '33333333', 1, 15, 3, 37, '2019-12-13 17:30:16', '2019-12-13 17:30:16');
INSERT INTO cashback_histories VALUES (7, 100, NULL, '44444444', 1, 15, 1, 37, '2019-12-13 19:00:15', '2019-12-13 19:00:15');
INSERT INTO cashback_histories VALUES (8, 1000, NULL, '56332', 1, 10, 1, 39, '2019-12-13 19:11:55', '2019-12-13 19:11:55');
INSERT INTO cashback_histories VALUES (9, 245, NULL, 'Кенгрпа', 1, 10, 1, 39, '2019-12-13 19:42:11', '2019-12-13 19:42:11');
INSERT INTO cashback_histories VALUES (10, 1000, NULL, '55555555', 1, 15, 1, 37, '2019-12-13 19:43:01', '2019-12-13 19:43:01');
INSERT INTO cashback_histories VALUES (11, 1000, '+380714320669', '123123', 1, 6, 1, 10, '2019-12-17 19:43:08', '2019-12-17 19:43:08');
INSERT INTO cashback_histories VALUES (12, 12312, '+380714320669', '3123123', 1, 6, 1, 10, '2019-12-17 19:43:13', '2019-12-17 19:43:13');
INSERT INTO cashback_histories VALUES (13, 123123, '+38213123', '123123', 0, 6, 1, NULL, '2019-12-17 19:43:18', '2019-12-17 19:43:18');
INSERT INTO cashback_histories VALUES (14, 123, '+380714320669', '231', 1, 6, 1, 10, '2019-12-17 19:43:24', '2019-12-17 19:43:24');
INSERT INTO cashback_histories VALUES (15, 2423523, '+380714320669', '523532', 1, 6, 1, 10, '2019-12-17 19:43:30', '2019-12-17 19:43:30');
INSERT INTO cashback_histories VALUES (16, 66, '+380714320669', '232', 1, 6, 1, 10, '2019-12-17 19:43:35', '2019-12-17 19:43:35');
INSERT INTO cashback_histories VALUES (17, 234234, '+380714320669', '2342', 1, 6, 1, 10, '2019-12-17 19:43:39', '2019-12-17 19:43:39');
INSERT INTO cashback_histories VALUES (18, 1, '+380714320669', '2344', 1, 6, 1, 10, '2019-12-17 19:43:44', '2019-12-17 19:43:44');
INSERT INTO cashback_histories VALUES (19, 34234, '+380714320669', '+380714320669', 1, 6, 1, 10, '2019-12-17 19:44:14', '2019-12-17 19:44:14');
INSERT INTO cashback_histories VALUES (20, 234234, '+380714320669', '2423', 1, 6, 1, 10, '2019-12-17 19:44:20', '2019-12-17 19:44:20');
INSERT INTO cashback_histories VALUES (21, 13123, '+380714320669', '21312', 1, 6, 1, 10, '2019-12-17 19:44:24', '2019-12-17 19:44:24');
INSERT INTO cashback_histories VALUES (22, 123, '+380714320669', '3123', 1, 6, 1, 10, '2019-12-17 19:44:28', '2019-12-17 19:44:28');
INSERT INTO cashback_histories VALUES (23, 123, '+380714320661', '132', 1, 6, 1, 2, '2019-12-20 20:40:35', '2019-12-20 20:40:35');
INSERT INTO cashback_histories VALUES (24, 100, '+380714320661', '1234', 1, 6, 1, 2, '2019-12-21 12:01:36', '2019-12-21 12:01:36');
INSERT INTO cashback_histories VALUES (25, 324, '4345', '1231233', 0, 6, 1, NULL, '2019-12-21 12:08:35', '2019-12-21 12:08:35');
INSERT INTO cashback_histories VALUES (26, 54433, '+380714320661', '5555', 1, 6, 1, 2, '2019-12-21 12:08:48', '2019-12-21 12:08:48');
INSERT INTO cashback_histories VALUES (27, 233, '+380714320661', '2222', 1, 6, 1, 2, '2019-12-21 12:36:21', '2019-12-21 12:36:21');
INSERT INTO cashback_histories VALUES (28, 500, NULL, '88888888', 1, 15, 2, 37, '2019-12-22 14:06:22', '2019-12-22 14:06:22');
INSERT INTO cashback_histories VALUES (29, 545, '+380714320661', '555', 1, 6, 2, 2, '2019-12-22 18:29:56', '2019-12-22 18:29:56');
INSERT INTO cashback_histories VALUES (30, 66565, '+380714320664', '6666', 0, 6, 2, NULL, '2019-12-22 18:30:13', '2019-12-22 18:30:13');
INSERT INTO cashback_histories VALUES (31, 444, '4', '4343', 0, 6, 2, NULL, '2019-12-22 18:30:35', '2019-12-22 18:30:35');
INSERT INTO cashback_histories VALUES (32, 2000, NULL, '23', 1, 10, 1, 10, '2020-01-14 12:49:41', '2020-01-14 12:49:41');
INSERT INTO cashback_histories VALUES (33, 2000, NULL, 'А', 1, 10, 1, 10, '2020-01-14 13:13:17', '2020-01-14 13:13:17');
INSERT INTO cashback_histories VALUES (34, 200, NULL, 'Вае', 1, 10, 1, 10, '2020-01-14 13:18:50', '2020-01-14 13:18:50');
INSERT INTO cashback_histories VALUES (35, 2080, NULL, 'Чммроо', 1, 10, 1, 10, '2020-01-14 13:24:53', '2020-01-14 13:24:53');
INSERT INTO cashback_histories VALUES (36, 22, NULL, 'П', 1, 10, 1, 10, '2020-01-14 13:33:51', '2020-01-14 13:33:51');
INSERT INTO cashback_histories VALUES (37, 100000, NULL, 'М', 1, 10, 1, 10, '2020-01-14 13:42:49', '2020-01-14 13:42:49');
INSERT INTO cashback_histories VALUES (38, 2090, NULL, '3', 1, 10, 1, 10, '2020-01-14 13:57:11', '2020-01-14 13:57:11');
INSERT INTO cashback_histories VALUES (39, 1000, NULL, 'Без чека', 1, 15, 2, 15, '2020-01-14 16:44:51', '2020-01-14 16:44:51');
INSERT INTO cashback_histories VALUES (40, 5000, NULL, 'Без чека', 1, 15, 1, 37, '2020-01-14 17:06:07', '2020-01-14 17:06:07');
INSERT INTO cashback_histories VALUES (41, 500, NULL, '11111111', 1, 15, 2, 37, '2020-01-14 18:58:18', '2020-01-14 18:58:18');
INSERT INTO cashback_histories VALUES (42, 500, NULL, '11111111', 1, 15, 2, 51, '2020-01-15 08:59:13', '2020-01-15 08:59:13');


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO categories VALUES (1, 'Еда и напитки', 'Акции, связанные с продажей продуктов питания и сервисами, основанными на продаже продуктов питания.', 'https://million-wallpapers.ru/wallpapers/1/71/15642823563272717331.jpg', '2019-11-30 19:27:38', '2020-01-19 08:39:04', 1);
INSERT INTO categories VALUES (2, 'Эстетика и образование', 'Модельные агенства', 'https://weproject.kz/media/userfiles/images/636066549882747495-1101828340_tumblr_nlq6y7NbXC1rrvuzmo1_1280.jpg', '2019-11-30 19:28:31', '2020-01-19 08:40:15', 2);


--
-- Data for Name: companies; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--


INSERT INTO companies VALUES (1, 'Ресторан "Большой Джон"', '50-летия СССР вулиця, 144/3, Донецк', 'Заведение самой вкусной городской пиццы.

"Big John" получила свое название в честь основателя Донецка - Джона Юза, мы уважаем нашу историю и требования заказчика!



Время работы - 11:00 - 21:00

Адрес: Донецк, ул.50-летия СССР, 144/3

Контактные телефоны: +38(050)268-47-77, +38(071)334-32-77', '+38(071) 334-32-77', 'mail@pizzdon.com', 'Андрей Лавин', 'https://sun9-71.userapi.com/c857616/v857616932/1020f8/QnT5gPlPRmQ.jpg', '2019-11-30 19:19:12', '2020-01-17 08:51:18', 6, 5, '');
INSERT INTO companies VALUES (6, 'Школа Бизнеса | Донецк', 'Донецк, ул.Университетская 36а', 'Первая и самая эффективная школа бизнеса в Донецке! Этот курс покажет и расскажет конкретные шаги для построение успешного бизнеса, которые будут полезны как новичкам, так и профессионалам. Наши преподаватели - действующие предприниматели или просто статусные люди Донецка с определенным опытом в своей сфере. Мы выбрали лучших из лучших для того, чтобы они поделились секретами своего успеха.



Наш адрес: Донецк, ул.Университетская 36а

Телефон: 0714990371', '+38(071) 499-03-71', '', 'Елена', 'https://sun1-26.userapi.com/dwtbpJt83giwVqfO4UPB9EsTM0P6JQQBUT5owA/DfAVAvUUNnI.jpg', '2020-01-17 17:36:19', '2020-01-17 17:36:19', 10, 5, '');
INSERT INTO companies VALUES (4, 'PIROGI DONETSK', 'Донецк, пл. Конституции, 1', 'Прекрасное заведение, в котором Вас порадуют вкуснейшими осетинскими пирогами и изысканными блюдами грузинской кухни!



Режим работы: 10:00 до 21:00



Имеется услуга доставки:

Центральные районы - 100 руб, остальные районы - до 250 руб.

+38(071)472-90-12', '+38(071) 472-90-12', '', 'Аксютина Юлия', 'https://sun1-21.userapi.com/PVY0rAmSX6D9xkLB-URRwy4SdXOXbLtEXlj5Hw/PCoaYIDw9Hk.jpg', '2020-01-11 18:09:53', '2020-01-16 09:28:01', 10, 5, 'https://t.me/pirogi_dn_bot');
INSERT INTO companies VALUES (5, '"Chicago Music Hall"', 'Донецк, ул.Артема, 123', '➨ Территория правильной музыки и крутых вечеринок.

➨ Мы работаем по точечным мероприятиям ( не каждые выходные)

➨ Следите за афишей мероприятий.



Бронь столов: +38(062)335-69-77 ( с феникса Бесплатно )', '+38(071) 451-18-69', '', 'Сергей', 'https://sun1-83.userapi.com/7sOSmQKa2lc461AhqXjzQE9_SLdvqKGY6NArHQ/nGitxk-PAus.jpg', '2020-01-17 08:14:08', '2020-01-17 08:15:27', 1, 5, '');
INSERT INTO companies VALUES (2, 'Ресторан Аркадия | Донецк', 'г. Донецк, ул. Набережная, 153А', 'Ресторан, который жарит вкусно!

Находится под управлением компании Rest Service



Режим работы: 10:00 до 21:00

Контактный телефон: +38(071)489-28-23

Доставка блюд по телефону: +38(071) 380-14-15

Бронь столиков по телефону: (071)489-28-23



Адрес: г. Донецк, ул. Набережная, 153А', '+38(071) 380-14-15', 'test2@gmail.com', 'Егор Шипилов', 'https://sun9-29.userapi.com/c846416/v846416159/b4ed1/b5mgMoEh8oE.jpg', '2019-11-30 19:22:36', '2020-01-17 08:34:33', 4, 5, 'https://t.me/arkadia_donetsk_bot');
INSERT INTO companies VALUES (3, 'Lotus Model Agency', 'ул. Артема, 100, Донецк, Донецкая область, 83000', 'Lotus Model Agency — Лидирующий Fashion проект в Донецком регионе

Агентство международного формата. Предоставляет услуги обучения и менеджмента моделей для девушек и парней всех возрастов.



Контактный номер: +38(071) 363-12-79, +38(071) 330-55-55

Адрес: Донецк, ул.Артема, 100 Д', '+38(071) 363-12-79', 'test@gmail.com', 'Виктория', 'https://sun1-26.userapi.com/JnTfKeKgeHYrEAxXDG6ABPQfqLULTvet_gRcMg/7mtke6u8g1o.jpg', '2019-11-30 19:26:25', '2020-01-17 08:45:06', 5, 5, 'https://t.me/lotus_model_bot');

--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO events VALUES (1, 'QUIZ', 'Ведущий Евгений Томазенко 

Команда от 2х до 8 чел

Бесплатное участие



Адрес: г. Донецк, ул. Набережная 153а



Телефон для справок: (071)489-28-23', 'https://sun9-52.userapi.com/c853628/v853628607/16f888/lKBfoTVN2s8.jpg', '2001-01-01 00:00:00', '2019-04-02 00:00:00', 2, '2019-12-03 09:02:12', '2019-12-23 14:53:51', 0);
INSERT INTO events VALUES (2, 'Детский кулинарный мастер-класс', 'Детский кулинарный мастер-класс "Клубничный штрудель" , стоимость участия 100руб., телефон для справок (071)489-28-23', 'https://sun9-33.userapi.com/c850608/v850608492/3a064/9bAw09PvWag.jpg', '2020-01-01 00:00:00', '2020-01-02 00:00:00', 2, '2019-12-03 09:04:29', '2020-01-19 08:05:08', 0);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO migrations VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO migrations VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO migrations VALUES (3, '2019_11_20_185755_create_user_has_promos_table', 1);
INSERT INTO migrations VALUES (4, '2019_11_20_1900000_create_promotions_table', 1);
INSERT INTO migrations VALUES (5, '2019_11_20_190159_create_companies_table', 1);
INSERT INTO migrations VALUES (6, '2019_11_20_192737_create_categories_table', 1);
INSERT INTO migrations VALUES (7, '2019_11_20_193310_create_refferals_histories_table', 1);
INSERT INTO migrations VALUES (8, '2019_11_20_193800_create_refferals_payment_histories_table', 1);
INSERT INTO migrations VALUES (10, '2019_11_20_211731_create_user_in_companies_table', 1);
INSERT INTO migrations VALUES (11, '2019_11_27_122253_create_events_table', 1);
INSERT INTO migrations VALUES (12, '2019_11_30_151405_create_user_infos_table', 1);
INSERT INTO migrations VALUES (14, '2019_11_20_203905_create_cashback_histories_table', 2);
INSERT INTO migrations VALUES (15, '2019_12_06_195229_create_stats_table', 3);
INSERT INTO migrations VALUES (16, '2019_12_06_195455_create_achievements_table', 3);
INSERT INTO migrations VALUES (17, '2019_12_06_200006_create_user_has_achievements_table', 3);
INSERT INTO migrations VALUES (18, '2019_12_23_125843_create_articles_table', 4);
INSERT INTO migrations VALUES (23, '2020_01_13_191820_create_prizes_table', 5);
INSERT INTO migrations VALUES (24, '2020_01_13_194919_create_promocodes_table', 5);
INSERT INTO migrations VALUES (25, '2020_01_16_084811_alter_companies_table', 6);


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--



--
-- Data for Name: prizes; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO prizes VALUES (2, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:48:50', '2020-01-14 10:48:50');
INSERT INTO prizes VALUES (4, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:48:55', '2020-01-14 10:48:55');
INSERT INTO prizes VALUES (5, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:48:56', '2020-01-14 10:48:56');
INSERT INTO prizes VALUES (8, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:49:02', '2020-01-14 10:49:02');
INSERT INTO prizes VALUES (10, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:49:07', '2020-01-14 10:49:07');
INSERT INTO prizes VALUES (12, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 0, true, 2, '2020-01-14 10:49:10', '2020-01-14 10:49:10');
INSERT INTO prizes VALUES (3, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 1, true, 2, '2020-01-14 10:48:52', '2020-01-14 12:17:15');
INSERT INTO prizes VALUES (7, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 3, true, 2, '2020-01-14 10:49:01', '2020-01-14 13:52:41');
INSERT INTO prizes VALUES (9, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 1, true, 2, '2020-01-14 10:49:04', '2020-01-14 13:54:35');
INSERT INTO prizes VALUES (14, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-14 14:05:10', '2020-01-14 14:05:10');
INSERT INTO prizes VALUES (15, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-14 14:05:13', '2020-01-14 14:05:13');
INSERT INTO prizes VALUES (11, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 1, true, 2, '2020-01-14 10:49:08', '2020-01-14 14:08:37');
INSERT INTO prizes VALUES (13, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 1, true, 3, '2020-01-14 14:03:48', '2020-01-14 14:09:35');
INSERT INTO prizes VALUES (1, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 1, true, 2, '2020-01-14 10:44:26', '2020-01-14 17:11:42');
INSERT INTO prizes VALUES (6, 'test', 'test', 'https://sun9-22.userapi.com/c854220/v854220254/1c23c4/JzYJ7foFtBw.jpg', 10000, 2, true, 2, '2020-01-14 10:48:59', '2020-01-14 20:25:35');
INSERT INTO prizes VALUES (16, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-14 20:26:26', '2020-01-14 20:26:26');
INSERT INTO prizes VALUES (17, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-14 20:26:32', '2020-01-14 20:26:32');
INSERT INTO prizes VALUES (18, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-14 20:26:36', '2020-01-14 20:26:36');
INSERT INTO prizes VALUES (19, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-20 10:31:42', '2020-01-20 10:31:42');
INSERT INTO prizes VALUES (20, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-20 10:31:44', '2020-01-20 10:31:44');
INSERT INTO prizes VALUES (21, 'Тест 2', 'тестовое описание', 'https://sun9-16.userapi.com/c857132/v857132254/aac1e/zIQwGLfMjro.jpg', 10000, 0, true, 3, '2020-01-20 10:31:46', '2020-01-20 10:31:46');


--
-- Data for Name: promocodes; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO promocodes VALUES (1, '12345', true, true, 10, 7, 2, '2020-01-14 10:03:57', '2020-01-14 13:53:12');
INSERT INTO promocodes VALUES (2, '12346', true, true, 10, 3, 2, '2020-01-14 12:16:33', '2020-01-14 13:53:14');
INSERT INTO promocodes VALUES (3, '123457', true, true, 10, 6, 2, '2020-01-14 12:21:38', '2020-01-14 13:53:15');
INSERT INTO promocodes VALUES (4, '0000', true, true, 10, 7, 2, '2020-01-14 13:52:13', '2020-01-14 13:53:17');
INSERT INTO promocodes VALUES (5, '0001', true, true, 10, 9, 2, '2020-01-14 13:54:12', '2020-01-14 17:02:27');
INSERT INTO promocodes VALUES (7, '003', true, true, 10, 13, 3, '2020-01-14 14:08:29', '2020-01-14 17:02:29');
INSERT INTO promocodes VALUES (6, '0002', true, true, 10, 11, 3, '2020-01-14 14:05:34', '2020-01-14 17:02:30');
INSERT INTO promocodes VALUES (8, '0007', false, false, NULL, NULL, 4, '2020-01-14 14:09:09', '2020-01-14 17:02:37');
INSERT INTO promocodes VALUES (9, 'промо1', false, true, NULL, NULL, 1, '2020-01-14 17:03:03', '2020-01-14 17:12:32');
INSERT INTO promocodes VALUES (10, 'промо2', true, true, 15, 1, 2, '2020-01-14 17:10:48', '2020-01-14 17:12:38');
INSERT INTO promocodes VALUES (11, '1111', true, false, 10, 6, 2, '2020-01-14 20:25:03', '2020-01-14 20:25:35');


--
-- Data for Name: promotions; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO promotions VALUES (4, 'Скидка 50% на "Рыбный Сет"', 'Вы истинный ценитель нежнейшего рыбного ассорти?

Тогда Вы просто обязаны насладиться нашим блюдом из свежайших и вкуснейших видов рыб - "Рыбный сет"!

Филе Окуня Свит Чили, Скумбрия, Корюшка, Хек, Кольца Кальмаров, Рыбные Палочки и несравненный фирменный соус.

Выход: 1250гр.

Цена: 1050₽

Цена со скидкой: 525₽



#Донецк #Акция #Аркадия #ЕдаИНапитки', 'https://sun1-94.userapi.com/13R3KtDfdpPjdMlce7DWf1hius_7X8hDlCPWHQ/7E8uwD1fPwg.jpg', NULL, '2020-01-10 06:00:00', '2020-02-10 06:00:00', 1000000, 2, 'г. Донецк, ул. Набережная, 153А', '48.012478, 37.821319', false, 'Благодарим за участие!', 0, 2, 1, '2020-01-10 16:10:47', '2020-01-14 18:28:55', 300);
INSERT INTO promotions VALUES (3, 'Скидка 50% на "Бургер на мангале от Шипилова"', 'Долгие годы дегустаций в различных городах мира, месяцы упорного изучения всех тонкостей и нюансов гастрономических предпочтений наших Клиентов, недели подбора лучших ингредиентов  - позволили приготовить для Вас Лучший бургер Мира - "Бургер на мангале от Шипилова".

Свежий и сочный салат, вкуснейший сыр, фирменная говяжья котлета, неповторимый соус, помидор и секретный ингредиент!

Выход 325гр.

Цена: 380₽

Цена со скидкой: 190₽



#Донецк #Акция #Аркадия #ЕдаИНапитки', 'https://sun1-30.userapi.com/O79nXzpCv_Ntwpnsw2JWMy8ce5_iU4eJ0Vf1Qg/h_sZATAM5cY.jpg', NULL, '2020-01-10 06:00:00', '2020-02-10 06:00:00', 1000000, 2, 'г. Донецк, ул. Набережная, 153А', '48.012478, 37.821319', false, 'Благодарим за участие!', 0, 2, 1, '2020-01-10 15:49:44', '2020-01-14 18:56:16', 200);
INSERT INTO promotions VALUES (1, 'Скидка 20% на обучение Lotus Model Agency', 'Lotus Model Agency — Лидирующий Fashion проект в Донецком регионе

Представляет собой агентство международного формата. Предоставляет услуги обучения и менеджмента моделей для девушек и парней всех возрастов



#LotusModelAgency #ЭстетикаИОбразование #Акция', 'http://lotus-model.ru/assets/app/img/logo.png', '/lotusprofile', '2020-01-10 06:00:00', '2020-02-10 06:00:00', 1000000, 8, 'г. Донецк, ул. Артема 100д', '48.017601,37.8013271', false, 'Благодарим за участие!', 0, 3, 2, '2019-12-01 08:40:54', '2020-01-10 16:25:21', 400);
INSERT INTO promotions VALUES (5, 'Скидка 50% на "Бургер сет Аркадия"', 'Пришло Ваше время получить гастрономическое удовольствие от разнообразия лучших бургеров нашего Ресторана:

1. Уникальный "Бургер от Шипилова" 

2. Величественный "Цезарь"

3. Самый сырный "Чизбургер"

4. Нежнейший "Фиш Бургер"

И в дополнение ко всему - картошка фри и картошка по-деревенски!

Выход 1700гр.

Цена: 1100₽

Цена со скидкой: 550₽



#Донецк #Акция #Аркадия #ЕдаИНапитки', 'https://sun1-27.userapi.com/aGU0bbcgzTeNAdzj4ff-7LXMA7H_sDnDQ-I8og/CmdbDntc2zo.jpg', NULL, '2020-01-10 06:00:00', '2020-02-10 06:00:00', 1000000, 4, 'г. Донецк, ул. Набережная, 153А', '48.012478, 37.821319', false, 'Благодарим за участие!', 0, 2, 1, '2020-01-10 16:51:57', '2020-01-15 08:57:30', 500);
INSERT INTO promotions VALUES (2, 'Скидка 50% на "Свиные рёбрышки на мангале по Чешскому рецепту с картошкой"!', 'Нежное, сочное, таящее во рту свиное мясо на ребрышках, приготовленное на открытом огне по уникальному Чешскому рецепту с вкуснейшим картофелем по-деревенски!

Выход: 700/150/50

Цена: 700₽

Цена со скидкой: 350₽



#Донецк #Акция #Аркадия #ЕдаИНапитки', 'https://sun1-83.userapi.com/v4k5Jnv9N9KUpreZFJxSwuSwiKkvShxOu_fH2A/U_GEyqNqdUk.jpg', NULL, '2020-01-17 06:00:00', '2020-02-17 06:00:00', 1000000, 4, 'г. Донецк, ул. Набережная, 153А', '48.012478, 37.821319', false, 'Благодарим за участие!', 0, 2, 1, '2020-01-10 15:23:05', '2020-01-17 17:49:19', 100);
INSERT INTO promotions VALUES (9, 'Скидка 20% на обучение в "Школе Бизнеса"', 'Пройди курс обучения, который покажет и расскажет конкретные шаги для построение успешного бизнеса.

Полученные знания будут полезны как новичкам, так и профессионалам. Занятия проводят действующие предприниматели или статусные люди Донецка с большим опытом в своей сфере.', 'https://sun1-26.userapi.com/dwtbpJt83giwVqfO4UPB9EsTM0P6JQQBUT5owA/DfAVAvUUNnI.jpg', NULL, '2020-01-17 06:00:00', '2020-01-17 06:00:00', 1000000, 0, 'Донецк, ул.Университетская, 36А', '48.009868, 37.798116', false, 'Благодарим за участие!', 0, 6, 2, '2020-01-17 17:46:04', '2020-01-17 17:49:48', 20);


--
-- Data for Name: refferals_histories; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO refferals_histories VALUES (2, 2, 5, false, '2019-11-30 21:38:39', '2019-11-30 21:38:39');
INSERT INTO refferals_histories VALUES (3, 2, 5, false, '2019-11-30 21:39:54', '2019-11-30 21:39:54');
INSERT INTO refferals_histories VALUES (4, 2, 5, false, '2019-11-30 21:42:50', '2019-11-30 21:42:50');
INSERT INTO refferals_histories VALUES (5, 10, 11, false, '2019-12-02 09:58:21', '2019-12-02 09:58:21');
INSERT INTO refferals_histories VALUES (6, 10, 13, false, '2019-12-03 09:06:18', '2019-12-03 09:06:18');
INSERT INTO refferals_histories VALUES (7, 10, 14, true, '2019-12-03 10:27:34', '2019-12-03 10:33:09');
INSERT INTO refferals_histories VALUES (9, 10, 2, false, '2019-12-04 20:18:00', '2019-12-04 20:18:00');
INSERT INTO refferals_histories VALUES (10, 15, 17, false, '2019-12-08 15:04:24', '2019-12-08 15:04:24');
INSERT INTO refferals_histories VALUES (11, 10, 34, false, '2019-12-08 19:37:08', '2019-12-08 19:37:08');
INSERT INTO refferals_histories VALUES (12, 10, 35, true, '2019-12-08 19:52:54', '2019-12-08 20:02:54');
INSERT INTO refferals_histories VALUES (15, 10, 40, false, '2019-12-20 18:13:18', '2019-12-20 18:13:18');
INSERT INTO refferals_histories VALUES (16, 10, 49, false, '2019-12-26 04:28:05', '2019-12-26 04:28:05');
INSERT INTO refferals_histories VALUES (17, 10, 50, false, '2019-12-26 19:08:06', '2019-12-26 19:08:06');
INSERT INTO refferals_histories VALUES (18, 10, 52, false, '2019-12-30 06:51:00', '2019-12-30 06:51:00');
INSERT INTO refferals_histories VALUES (19, 10, 53, false, '2020-01-06 00:37:42', '2020-01-06 00:37:42');
INSERT INTO refferals_histories VALUES (20, 10, 54, false, '2020-01-06 15:46:28', '2020-01-06 15:46:28');
INSERT INTO refferals_histories VALUES (21, 10, 55, false, '2020-01-08 12:28:22', '2020-01-08 12:28:22');
INSERT INTO refferals_histories VALUES (54, 10, 88, false, '2020-01-11 14:38:38', '2020-01-11 14:38:38');
INSERT INTO refferals_histories VALUES (55, 10, 39, false, '2020-01-11 16:07:41', '2020-01-11 16:07:41');
INSERT INTO refferals_histories VALUES (56, 10, 89, false, '2020-01-11 17:48:34', '2020-01-11 17:48:34');
INSERT INTO refferals_histories VALUES (57, 10, 90, false, '2020-01-11 18:56:43', '2020-01-11 18:56:43');
INSERT INTO refferals_histories VALUES (58, 38, 92, false, '2020-01-12 15:30:53', '2020-01-12 15:30:53');
INSERT INTO refferals_histories VALUES (59, 38, 93, false, '2020-01-13 13:10:19', '2020-01-13 13:10:19');
INSERT INTO refferals_histories VALUES (8, 10, 10, true, '2019-12-04 19:58:23', '2020-01-14 14:01:47');
INSERT INTO refferals_histories VALUES (14, 37, 15, true, '2019-12-13 16:36:56', '2020-01-14 16:44:51');
INSERT INTO refferals_histories VALUES (13, 15, 37, true, '2019-12-13 16:35:51', '2020-01-14 18:59:20');


--
-- Data for Name: refferals_payment_histories; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO refferals_payment_histories VALUES (2, 2, 1, 1, 200, '2019-11-30 20:35:57', '2019-11-30 20:35:57');
INSERT INTO refferals_payment_histories VALUES (3, 2, 1, 1, 2, '2019-11-30 20:36:12', '2019-11-30 20:36:12');
INSERT INTO refferals_payment_histories VALUES (4, 2, 1, 1, 209, '2019-11-30 20:37:26', '2019-11-30 20:37:26');
INSERT INTO refferals_payment_histories VALUES (5, 2, 1, 3, 200, '2019-11-30 20:51:24', '2019-11-30 20:51:24');
INSERT INTO refferals_payment_histories VALUES (1, 2, 6, 3, 200, '2019-11-30 20:35:27', '2019-11-30 20:35:27');
INSERT INTO refferals_payment_histories VALUES (6, 37, 15, 1, 5, '2019-12-13 17:20:05', '2019-12-13 17:20:05');
INSERT INTO refferals_payment_histories VALUES (7, 37, 15, 1, 45, '2019-12-13 17:24:17', '2019-12-13 17:24:17');
INSERT INTO refferals_payment_histories VALUES (8, 37, 15, 2, 25, '2019-12-13 17:29:26', '2019-12-13 17:29:26');
INSERT INTO refferals_payment_histories VALUES (9, 37, 15, 3, 50, '2019-12-13 17:30:51', '2019-12-13 17:30:51');
INSERT INTO refferals_payment_histories VALUES (10, 37, 15, 1, 5, '2019-12-13 18:57:34', '2019-12-13 18:57:34');
INSERT INTO refferals_payment_histories VALUES (11, 39, 10, 1, 22, '2019-12-13 19:13:56', '2019-12-13 19:13:56');
INSERT INTO refferals_payment_histories VALUES (12, 39, 10, 1, 10, '2019-12-13 19:20:07', '2019-12-13 19:20:07');
INSERT INTO refferals_payment_histories VALUES (13, 37, 15, 1, 50, '2019-12-13 19:43:51', '2019-12-13 19:43:51');
INSERT INTO refferals_payment_histories VALUES (14, 10, 10, 1, 200, '2020-01-14 11:17:13', '2020-01-14 11:17:13');
INSERT INTO refferals_payment_histories VALUES (15, 10, 10, 1, 200, '2020-01-14 11:17:15', '2020-01-14 11:17:15');
INSERT INTO refferals_payment_histories VALUES (16, 10, 10, 1, 200, '2020-01-14 11:17:19', '2020-01-14 11:17:19');
INSERT INTO refferals_payment_histories VALUES (17, 10, 10, 1, 0, '2020-01-14 12:28:02', '2020-01-14 12:28:02');
INSERT INTO refferals_payment_histories VALUES (18, 10, 10, 1, 0, '2020-01-14 12:28:09', '2020-01-14 12:28:09');
INSERT INTO refferals_payment_histories VALUES (19, 10, 10, 1, 200, '2020-01-14 12:28:31', '2020-01-14 12:28:31');
INSERT INTO refferals_payment_histories VALUES (20, 10, 10, 1, 200, '2020-01-14 12:28:36', '2020-01-14 12:28:36');
INSERT INTO refferals_payment_histories VALUES (21, 10, 10, 1, 200, '2020-01-14 12:28:44', '2020-01-14 12:28:44');
INSERT INTO refferals_payment_histories VALUES (22, 10, 10, 1, 200, '2020-01-14 12:51:08', '2020-01-14 12:51:08');
INSERT INTO refferals_payment_histories VALUES (23, 10, 10, 1, 200, '2020-01-14 13:09:56', '2020-01-14 13:09:56');
INSERT INTO refferals_payment_histories VALUES (24, 10, 10, 1, 200, '2020-01-14 13:10:07', '2020-01-14 13:10:07');
INSERT INTO refferals_payment_histories VALUES (25, 10, 10, 1, 200, '2020-01-14 13:10:18', '2020-01-14 13:10:18');
INSERT INTO refferals_payment_histories VALUES (26, 10, 10, 1, 200, '2020-01-14 13:10:39', '2020-01-14 13:10:39');
INSERT INTO refferals_payment_histories VALUES (27, 10, 10, 1, 200, '2020-01-14 13:11:14', '2020-01-14 13:11:14');
INSERT INTO refferals_payment_histories VALUES (28, 10, 10, 1, 200, '2020-01-14 13:12:17', '2020-01-14 13:12:17');
INSERT INTO refferals_payment_histories VALUES (29, 10, 10, 1, 20, '2020-01-14 14:01:27', '2020-01-14 14:01:27');
INSERT INTO refferals_payment_histories VALUES (30, 10, 10, 1, 2000, '2020-01-14 14:01:47', '2020-01-14 14:01:47');
INSERT INTO refferals_payment_histories VALUES (31, 37, 15, 2, 240, '2020-01-14 17:07:20', '2020-01-14 17:07:20');
INSERT INTO refferals_payment_histories VALUES (32, 37, 15, 2, 25, '2020-01-14 18:59:20', '2020-01-14 18:59:20');


--
-- Data for Name: stats; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO stats VALUES (2, 6, 2, 34, '2019-12-08 19:37:09', '2019-12-08 19:38:47');
INSERT INTO stats VALUES (90, 2, 5532, 10, '2020-01-14 13:13:15', '2020-01-14 13:57:11');
INSERT INTO stats VALUES (4, 0, 1, 35, '2019-12-08 20:02:53', '2019-12-08 20:02:53');
INSERT INTO stats VALUES (89, 9, 2220, 10, '2020-01-14 13:12:18', '2020-01-14 14:01:47');
INSERT INTO stats VALUES (91, 2, 50, 15, '2020-01-14 16:44:51', '2020-01-14 16:44:51');
INSERT INTO stats VALUES (10, 2, 480, 37, '2019-12-13 17:23:00', '2020-01-14 18:58:17');
INSERT INTO stats VALUES (9, 9, 445, 37, '2019-12-13 17:20:05', '2020-01-14 18:59:20');
INSERT INTO stats VALUES (5, 7, 42, 10, '2019-12-08 20:02:57', '2020-01-15 05:20:46');
INSERT INTO stats VALUES (6, 3, 2400, 10, '2019-12-08 21:31:25', '2019-12-09 06:14:41');
INSERT INTO stats VALUES (77, 0, 3, 10, '2020-01-11 15:59:46', '2020-01-15 05:20:46');
INSERT INTO stats VALUES (16, 7, 72, 37, '2019-12-25 08:52:27', '2020-01-15 06:10:47');
INSERT INTO stats VALUES (17, 0, 7, 37, '2019-12-25 08:55:26', '2020-01-15 06:10:47');
INSERT INTO stats VALUES (12, 9, 32, 39, '2019-12-13 19:13:56', '2019-12-13 19:20:07');
INSERT INTO stats VALUES (11, 2, 62, 39, '2019-12-13 19:11:55', '2019-12-13 19:42:11');
INSERT INTO stats VALUES (36, 7, 30, 51, '2020-01-04 07:01:24', '2020-01-15 08:57:30');
INSERT INTO stats VALUES (92, 0, 2, 51, '2020-01-15 08:57:31', '2020-01-15 08:57:31');
INSERT INTO stats VALUES (13, 6, 1, 40, '2019-12-20 18:13:18', '2019-12-20 18:13:18');
INSERT INTO stats VALUES (8, 1, 1, 37, '2019-12-13 16:36:56', '2019-12-25 08:52:27');
INSERT INTO stats VALUES (7, 1, 1, 15, '2019-12-13 16:35:50', '2019-12-25 08:55:26');
INSERT INTO stats VALUES (93, 2, 50, 51, '2020-01-15 08:59:12', '2020-01-15 08:59:13');
INSERT INTO stats VALUES (18, 6, 1, 49, '2019-12-26 04:28:04', '2019-12-26 04:28:04');
INSERT INTO stats VALUES (19, 6, 1, 50, '2019-12-26 19:08:06', '2019-12-26 19:08:06');
INSERT INTO stats VALUES (20, 6, 1, 52, '2019-12-30 06:51:00', '2019-12-30 06:51:00');
INSERT INTO stats VALUES (22, 7, 10, 16, '2020-01-04 07:00:57', '2020-01-04 07:00:57');
INSERT INTO stats VALUES (23, 7, 10, 2, '2020-01-04 07:00:59', '2020-01-04 07:00:59');
INSERT INTO stats VALUES (24, 7, 10, 6, '2020-01-04 07:01:01', '2020-01-04 07:01:01');
INSERT INTO stats VALUES (25, 7, 10, 11, '2020-01-04 07:01:02', '2020-01-04 07:01:02');
INSERT INTO stats VALUES (26, 7, 10, 49, '2020-01-04 07:01:04', '2020-01-04 07:01:04');
INSERT INTO stats VALUES (27, 7, 10, 17, '2020-01-04 07:01:07', '2020-01-04 07:01:07');
INSERT INTO stats VALUES (28, 7, 10, 13, '2020-01-04 07:01:09', '2020-01-04 07:01:09');
INSERT INTO stats VALUES (29, 7, 10, 50, '2020-01-04 07:01:11', '2020-01-04 07:01:11');
INSERT INTO stats VALUES (30, 7, 10, 12, '2020-01-04 07:01:14', '2020-01-04 07:01:14');
INSERT INTO stats VALUES (31, 7, 10, 8, '2020-01-04 07:01:15', '2020-01-04 07:01:15');
INSERT INTO stats VALUES (32, 7, 10, 40, '2020-01-04 07:01:17', '2020-01-04 07:01:17');
INSERT INTO stats VALUES (33, 7, 10, 36, '2020-01-04 07:01:19', '2020-01-04 07:01:19');
INSERT INTO stats VALUES (34, 7, 10, 42, '2020-01-04 07:01:20', '2020-01-04 07:01:20');
INSERT INTO stats VALUES (35, 7, 10, 39, '2020-01-04 07:01:22', '2020-01-04 07:01:22');
INSERT INTO stats VALUES (37, 7, 10, 52, '2020-01-04 07:01:25', '2020-01-04 07:01:25');
INSERT INTO stats VALUES (94, 7, 20, 94, '2020-01-15 09:42:55', '2020-01-15 09:42:55');
INSERT INTO stats VALUES (3, 7, 11, 35, '2019-12-08 20:02:51', '2020-01-04 07:01:28');
INSERT INTO stats VALUES (38, 7, 10, 53, '2020-01-06 00:37:42', '2020-01-06 00:37:42');
INSERT INTO stats VALUES (39, 6, 1, 53, '2020-01-06 00:37:42', '2020-01-06 00:37:42');
INSERT INTO stats VALUES (40, 7, 10, 54, '2020-01-06 15:46:28', '2020-01-06 15:46:28');
INSERT INTO stats VALUES (41, 6, 1, 54, '2020-01-06 15:46:28', '2020-01-06 15:46:28');
INSERT INTO stats VALUES (42, 7, 10, 55, '2020-01-08 12:28:21', '2020-01-08 12:28:21');
INSERT INTO stats VALUES (43, 6, 1, 55, '2020-01-08 12:28:21', '2020-01-08 12:28:21');
INSERT INTO stats VALUES (95, 7, 20, 95, '2020-01-17 10:44:16', '2020-01-17 10:44:16');
INSERT INTO stats VALUES (96, 7, 20, 113, '2020-01-20 06:23:09', '2020-01-20 06:23:09');
INSERT INTO stats VALUES (97, 7, 20, 114, '2020-01-20 17:56:14', '2020-01-20 17:56:15');
INSERT INTO stats VALUES (14, 7, 52, 15, '2019-12-25 08:52:27', '2020-01-10 16:54:39');
INSERT INTO stats VALUES (15, 0, 5, 15, '2019-12-25 08:52:27', '2020-01-10 16:54:39');
INSERT INTO stats VALUES (75, 7, 10, 88, '2020-01-11 14:38:38', '2020-01-11 14:38:38');
INSERT INTO stats VALUES (76, 6, 1, 88, '2020-01-11 14:38:38', '2020-01-11 14:38:38');
INSERT INTO stats VALUES (1, 1, 5, 10, '2019-12-08 19:35:21', '2020-01-11 15:59:46');
INSERT INTO stats VALUES (78, 6, 1, 39, '2020-01-11 16:07:41', '2020-01-11 16:07:41');
INSERT INTO stats VALUES (79, 7, 10, 89, '2020-01-11 17:48:34', '2020-01-11 17:48:34');
INSERT INTO stats VALUES (80, 6, 1, 89, '2020-01-11 17:48:34', '2020-01-11 17:48:34');
INSERT INTO stats VALUES (81, 7, 10, 90, '2020-01-11 18:56:43', '2020-01-11 18:56:43');
INSERT INTO stats VALUES (82, 6, 1, 90, '2020-01-11 18:56:43', '2020-01-11 18:56:43');
INSERT INTO stats VALUES (83, 7, 10, 91, '2020-01-12 14:15:46', '2020-01-12 14:15:46');
INSERT INTO stats VALUES (21, 7, 20, 38, '2020-01-04 07:00:56', '2020-01-12 15:14:27');
INSERT INTO stats VALUES (84, 0, 1, 38, '2020-01-12 15:14:27', '2020-01-12 15:14:27');
INSERT INTO stats VALUES (85, 7, 10, 92, '2020-01-12 15:30:53', '2020-01-12 15:30:53');
INSERT INTO stats VALUES (86, 6, 1, 92, '2020-01-12 15:30:53', '2020-01-12 15:30:53');
INSERT INTO stats VALUES (87, 7, 10, 93, '2020-01-13 13:10:19', '2020-01-13 13:10:19');
INSERT INTO stats VALUES (88, 6, 1, 93, '2020-01-13 13:10:19', '2020-01-13 13:10:19');


--
-- Data for Name: user_has_achievements; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO user_has_achievements VALUES (7, true, 10, 1, '2019-12-09 06:14:44', '2019-12-09 06:14:44');
INSERT INTO user_has_achievements VALUES (8, false, 10, 4, '2020-01-14 13:32:37', '2020-01-14 13:32:37');
INSERT INTO user_has_achievements VALUES (9, false, 10, 11, '2020-01-14 14:01:47', '2020-01-14 14:01:47');


--
-- Data for Name: user_has_promos; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO user_has_promos VALUES (1, 10, 1, '2019-12-01 16:26:10', '2019-12-01 16:26:10');
INSERT INTO user_has_promos VALUES (2, 10, 1, '2019-12-01 16:30:08', '2019-12-01 16:30:08');
INSERT INTO user_has_promos VALUES (3, 10, 1, '2019-12-01 16:30:25', '2019-12-01 16:30:25');
INSERT INTO user_has_promos VALUES (4, 10, 1, '2019-12-01 16:30:46', '2019-12-01 16:30:46');
INSERT INTO user_has_promos VALUES (5, 14, 1, '2019-12-03 10:33:09', '2019-12-03 10:33:09');
INSERT INTO user_has_promos VALUES (6, 35, 1, '2019-12-08 20:02:50', '2019-12-08 20:02:50');
INSERT INTO user_has_promos VALUES (7, 15, 1, '2019-12-25 08:52:27', '2019-12-25 08:52:27');
INSERT INTO user_has_promos VALUES (8, 37, 1, '2019-12-25 08:55:25', '2019-12-25 08:55:25');
INSERT INTO user_has_promos VALUES (9, 15, 2, '2020-01-10 15:25:57', '2020-01-10 15:25:57');
INSERT INTO user_has_promos VALUES (10, 15, 3, '2020-01-10 15:52:36', '2020-01-10 15:52:36');
INSERT INTO user_has_promos VALUES (11, 15, 4, '2020-01-10 16:17:20', '2020-01-10 16:17:20');
INSERT INTO user_has_promos VALUES (12, 15, 5, '2020-01-10 16:54:39', '2020-01-10 16:54:39');
INSERT INTO user_has_promos VALUES (13, 10, 5, '2020-01-11 15:59:46', '2020-01-11 15:59:46');
INSERT INTO user_has_promos VALUES (14, 38, 2, '2020-01-12 15:14:26', '2020-01-12 15:14:26');
INSERT INTO user_has_promos VALUES (15, 37, 2, '2020-01-14 18:27:02', '2020-01-14 18:27:02');
INSERT INTO user_has_promos VALUES (16, 37, 4, '2020-01-14 18:28:55', '2020-01-14 18:28:55');
INSERT INTO user_has_promos VALUES (17, 37, 3, '2020-01-14 18:56:16', '2020-01-14 18:56:16');
INSERT INTO user_has_promos VALUES (18, 37, 5, '2020-01-14 19:31:06', '2020-01-14 19:31:06');
INSERT INTO user_has_promos VALUES (19, 10, 2, '2020-01-15 05:20:45', '2020-01-15 05:20:45');
INSERT INTO user_has_promos VALUES (20, 37, 8, '2020-01-15 06:10:46', '2020-01-15 06:10:46');
INSERT INTO user_has_promos VALUES (21, 51, 5, '2020-01-15 08:57:30', '2020-01-15 08:57:30');


--
-- Data for Name: user_in_companies; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO user_in_companies VALUES (1, 1, 1, '2019-11-30 19:29:42', '2019-11-30 19:29:42');
INSERT INTO user_in_companies VALUES (2, 1, 2, '2019-11-30 19:29:43', '2019-11-30 19:29:43');
INSERT INTO user_in_companies VALUES (3, 1, 3, '2019-11-30 19:29:43', '2019-11-30 19:29:43');
INSERT INTO user_in_companies VALUES (11, 10, 1, '2019-12-08 15:13:39', '2019-12-08 15:13:39');
INSERT INTO user_in_companies VALUES (15, 6, 2, '2019-12-21 15:04:51', '2019-12-21 15:04:51');
INSERT INTO user_in_companies VALUES (16, 6, 1, '2019-12-21 15:04:51', '2019-12-21 15:04:51');
INSERT INTO user_in_companies VALUES (17, 6, 3, '2019-12-21 15:04:51', '2019-12-21 15:04:51');
INSERT INTO user_in_companies VALUES (23, 15, 2, '2019-12-25 09:16:18', '2019-12-25 09:16:18');
INSERT INTO user_in_companies VALUES (24, 15, 1, '2019-12-25 09:16:18', '2019-12-25 09:16:18');
INSERT INTO user_in_companies VALUES (25, 15, 3, '2019-12-25 09:16:18', '2019-12-25 09:16:18');
INSERT INTO user_in_companies VALUES (26, 37, 2, '2020-01-10 15:25:24', '2020-01-10 15:25:24');
INSERT INTO user_in_companies VALUES (27, 37, 1, '2020-01-10 15:25:24', '2020-01-10 15:25:24');
INSERT INTO user_in_companies VALUES (28, 37, 3, '2020-01-10 15:25:24', '2020-01-10 15:25:24');
INSERT INTO user_in_companies VALUES (29, 51, 2, '2020-01-16 05:39:15', '2020-01-16 05:39:15');
INSERT INTO user_in_companies VALUES (30, 94, 2, '2020-01-16 05:40:07', '2020-01-16 05:40:07');


--
-- Data for Name: user_info; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--



--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: vrawpxxujbruhc
--

INSERT INTO users VALUES (16, 'viktoriya_bogatyriova', '765765332@t.me', NULL, '$2y$10$cvIWLB4MhrZ0w4/.J/bJJuh0YBGl11JIHqABPpWPpVKSPugVoqh5i', 'Виктория Богатырева', '', NULL, '', NULL, NULL, NULL, NULL, '000', '765765332', 0, 10, 0, false, NULL, '2019-12-05 06:18:45', '2019-12-25 12:33:21', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (53, 'drdorik', '143161856@t.me', NULL, '$2y$10$OScW2sNxehvNWpRGI.Gc..mn3F3gSDQbSyRUf1M0PAgSVVbkN9EBW', 'Vladislav Dorsman', '', NULL, '', NULL, NULL, NULL, NULL, '000', '143161856', 0, 10, 0, false, NULL, '2020-01-06 00:37:42', '2020-01-06 00:37:42', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (2, '997456677', '997456677@t.me', NULL, '$2y$10$XHOXoU/Mk9V6NWfr9zQez.DthvISkk29uffgyYEkSO8CRqAuat492', ''' melfina o''shea ''', '855', '+380714320661', '', 'Донецк', 0, 12, 'Донецк', '000', '997456677', 3, 0, 3277, true, NULL, '2019-11-30 19:06:09', '2019-12-25 12:33:22', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (6, 'Aleks', 'admin@gmail.com', NULL, '$2y$10$JLRdhQvMMUuTqvlfTVvziOMx0Bu3/dh2sxuPUqjqvD0PXZR1WYA2m', 'Алексей', '', NULL, '', NULL, NULL, NULL, NULL, '000', '484698705', 0, 10000, 10000, true, 'DNerKPARmftJHqKxVplXrQfZ8wSTVfSZdINselip3uOrJFjUZRhqoscWkYc2', '2019-12-01 08:34:14', '2019-12-21 15:04:51', 12, 1, 1, 0, 150);
INSERT INTO users VALUES (11, '576116481', '576116481@t.me', NULL, '$2y$10$vlwusR1l0FZJOpFp3fk6te5i8OIn50hnDt00oOakNidf.J00sb64S', 'Богдан ', 'Богдан', '+380713314823', '', 'Донецк', 0, NULL, '11.09.1998', '000', '576116481', 0, 10, 0, false, NULL, '2019-12-02 09:58:20', '2019-12-03 04:23:19', 12, 1, 3, 0, 0);
INSERT INTO users VALUES (49, '364262473', '364262473@t.me', NULL, '$2y$10$YLGSInF05Vg2R1Uh8WqUIe.cSHxHTXqytQG0Oe5KyJb1FeH9JZGb.', 'Tolik Trandafilov', '', NULL, '', NULL, NULL, NULL, NULL, '000', '364262473', 0, 10, 0, false, NULL, '2019-12-26 04:28:04', '2019-12-26 04:28:04', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (17, 'lerrrasik', '683263470@t.me', NULL, '$2y$10$ltp8NGuJOWtIqiI4ISZ.GOYuMgJmgJZ/DEL2ftaaUhWhdJSjRYiO.', 'Valeriya Selistranova', 'Валерия', '+380713050410', '', 'Донецк', 1, NULL, '24.03.2001', '000', '683263470', 0, 10, 0, false, NULL, '2019-12-08 15:04:23', '2019-12-08 17:09:06', 15, 0, 0, 0, 0);
INSERT INTO users VALUES (13, '738491314', '738491314@t.me', NULL, '$2y$10$kOkU7LIzNkziOC4Fi7S95uh3EkzaYsSs6QcrMCLehYIN4LDDdqswW', 'Александр Лихолетов', 'Александр', '+38123', '', 'Донецк', 0, 29, '5', '000', '738491314', 0, 10, 0, false, NULL, '2019-12-03 09:06:17', '2019-12-03 10:20:17', 10, 0, 3, 0, 0);
INSERT INTO users VALUES (50, 'deadolegovich', '486810927@t.me', NULL, '$2y$10$gPWZ1fpdPf/y3M6L66l0ZuqAzAa9M1xR3pUR0Nd34voZlUNnuTtd2', 'Илья Олегович', '', NULL, '', NULL, NULL, NULL, NULL, '000', '486810927', 0, 10, 0, false, NULL, '2019-12-26 19:08:06', '2019-12-26 19:08:06', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (12, '384166359', '384166359@t.me', NULL, '$2y$10$X7Zys9U8/FPwanQ5Wg0EQu3RMOzG915r9gyzQyHo09.30bz7f36PG', 'Dima dima', '', NULL, '', NULL, NULL, NULL, NULL, '000', '384166359', 0, 10, 0, false, NULL, '2019-12-03 06:55:40', '2019-12-08 21:14:38', 8, 1, 3, 2, 0);
INSERT INTO users VALUES (8, 'mari_tl', '436013756@t.me', NULL, '$2y$10$3d696MzwUCwEw0dSqrGut.XMFYNIfDVZSv732y33vFv9UITjx8VNm', 'Марина ', 'M', '+38566', '', NULL, NULL, 18, NULL, '000', '436013756', 0, 10, 0, false, NULL, '2019-12-01 11:42:18', '2019-12-08 21:14:39', 10, 1, 3, 5, 0);
INSERT INTO users VALUES (15, '578769289', '578769289@t.me', NULL, '$2y$10$ZIqAm.2ubbukNJc4hItQYOO0hZhPa.R3wesB2djOTdrrYZ0wd76tS', 'Andrey Yasinovskiy', 'Андрей', '+380713943435', '', 'Донецк', 0, 31, '19 мая 1988', '000', '578769289', 2, 20, 100, true, NULL, '2019-12-04 11:16:13', '2020-01-15 08:58:32', 37, 1, 0, 0, 1);
INSERT INTO users VALUES (40, '631031222', '631031222@t.me', NULL, '$2y$10$XG7MLTzzQkD1nXI2UVZIVOzWJYwVn0TDDdWPcI51ppLNT/wk9oeTy', 'Анна Жур', '', NULL, '', NULL, NULL, NULL, NULL, '000', '631031222', 0, 10, 0, false, NULL, '2019-12-20 18:13:18', '2019-12-20 18:13:18', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (36, 'ivanov00011', '214657565@t.me', NULL, '$2y$10$2JKGQbmW7A5jdJznV7p3Q.P7uCd/gY3iCa1fnumPMZhzDuw41Bl6q', 'Иван Иванов', '', NULL, '', NULL, NULL, NULL, NULL, '000', '214657565', 0, 10, 0, false, NULL, '2019-12-13 11:20:47', '2019-12-25 12:33:20', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (38, '526653858', '526653858@t.me', NULL, '$2y$10$nJPzY6vbtPngRfDiNTDinez.WBbi8tVl0YXj8LhnftxLNWHwBOi96', 'Егор Шипилов', 'Егор', '+380713169564', '', 'Донецк', 0, NULL, '3 ноября 1986', '000', '526653858', 2, 10, 0, false, NULL, '2019-12-13 18:34:25', '2020-01-13 13:10:19', 42, 1, 0, 0, 0);
INSERT INTO users VALUES (89, '515377168', '515377168@t.me', NULL, '$2y$10$rKq/bJT.0wLxITgCunXwkuyoUVCvkAAv9/pWFkrU0kXs4ZfuRCzo2', '74332 ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '515377168', 0, 10, 0, false, NULL, '2020-01-11 17:48:34', '2020-01-11 17:48:34', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (94, '835875829', '835875829@t.me', NULL, '$2y$10$TCO6c9IA7q69v5peGQ2GDekwSfnedZlQdroLNbSoqPoCIvEKy5yGW', 'Тимановская Анна', '', NULL, '', NULL, NULL, NULL, NULL, '000', '835875829', 0, 10, 0, true, NULL, '2020-01-15 09:42:55', '2020-01-16 05:40:07', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (52, '385752716', '385752716@t.me', NULL, '$2y$10$FF04HbIAbHLecoju3ANQkuE2NMvm74QAwDg/CY8tI.7StQPd8qKkS', 'Андрей Захаров', '', NULL, '', NULL, NULL, NULL, NULL, '000', '385752716', 0, 10, 0, false, NULL, '2019-12-30 06:51:00', '2019-12-30 06:51:00', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (90, '777396094', '777396094@t.me', NULL, '$2y$10$7TSftO6OSvmAx.esXr9xxO5bemEATvJ4wXQmN0uipfJvUqDybw0zm', 'Nastya ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '777396094', 0, 10, 0, false, NULL, '2020-01-11 18:56:43', '2020-01-11 18:56:43', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (54, '669772518', '669772518@t.me', NULL, '$2y$10$.HJxMnCP5Kyiuxk5QtiQYOPWBX39yQiHJ0pn9o.fD1l43vFnYhHim', 'Спасатель ', 'Вадим', '+380714162130', '', 'Донецк', 0, NULL, '22.11.2001', '000', '669772518', 0, 10, 0, false, NULL, '2020-01-06 15:46:28', '2020-01-06 15:48:31', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (55, '570441343', '570441343@t.me', NULL, '$2y$10$1bNgVwHbBSoDWPq1kpn3s.HOM0ZR.Ae0piAhraHnp.9uOJnujdXue', 'Оля ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '570441343', 0, 10, 0, false, NULL, '2020-01-08 12:28:21', '2020-01-08 12:28:21', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (88, '747071598', '747071598@t.me', NULL, '$2y$10$eyDn/joMaSi/RXW0MToDv.oBDkxOUVAssrlik7Ham2yhqQrcCWjIS', 'Владимир ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '747071598', 0, 10, 0, false, NULL, '2020-01-11 14:38:38', '2020-01-11 14:38:38', 10, 0, 0, 0, 0);
INSERT INTO users VALUES (39, '997456696', '997456696@t.me', NULL, '$2y$10$P5RftuO4nxBPgmiGT5LfTOeAl.RBFTkqmdLY/GeNIu0BRmNZ4s/sW', ''' melfina o''shea ''', 'Лиза', '+380713664959', '', 'Макеевка', 1, NULL, '23.11.1999', '000', '997456696', 0, 0, 40, false, NULL, '2019-12-13 18:51:33', '2020-01-11 16:15:09', 10, 1, 0, 0, 0);
INSERT INTO users VALUES (93, '807410560', '807410560@t.me', NULL, '$2y$10$Vh8M43KbKkW.2.p8QT1BWuEC8IrEFkBw3IhZIKm.ngTnxs8ayVdqK', 'Александр Алекс', '', NULL, '', NULL, NULL, NULL, NULL, '000', '807410560', 0, 10, 0, false, NULL, '2020-01-13 13:10:19', '2020-01-13 13:10:19', 38, 0, 0, 0, 0);
INSERT INTO users VALUES (91, 'etalunicum', '273554652@t.me', NULL, '$2y$10$DfiOxXHMcZDznyGHFIirku1HPtcL/pBrqI80Q68.jV/IfouRJxMsm', 'Georgiy Zhukov ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '273554652', 0, 10, 0, false, NULL, '2020-01-12 14:15:46', '2020-01-12 14:15:47', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (37, '884478064', '884478064@t.me', NULL, '$2y$10$fSb24X9ADTsEuqFGvlYoJ.4RfyWRKZYBmYBZoWhba3pdyVYpt/pgW', 'Виктор ', '1', '+380713764282', '', 'Донецк', 0, 15, '19 февраля 1952', '000', '884478064', 1, 0, 55, true, NULL, '2019-12-13 16:35:50', '2020-01-15 06:10:47', 15, 1, 0, 0, 1);
INSERT INTO users VALUES (51, '469949620', '469949620@t.me', NULL, '$2y$10$SJYoW2H8HfMzV0hspBzITu1QzQwqfXPxvLOUGUZmksbVtw13ybQ9a', 'Ilya Greblev', 'Илья', '+380713989638', '', 'Донецк', 0, NULL, '15.04.1992', '000', '469949620', 0, 10, 2225, true, NULL, '2019-12-28 12:41:03', '2020-01-16 05:39:15', 42, 1, 0, 0, 0);
INSERT INTO users VALUES (113, 'Alex_R17', '530513785@t.me', NULL, '$2y$10$lSIlgImF0ouIwTj.zsbX3.ynRdJr50HrQ8Bkae4kyJjS6LBtTHRoy', 'Алексей Авдеенко', '', NULL, '', NULL, NULL, NULL, NULL, '000', '530513785', 0, 10, 0, false, NULL, '2020-01-20 06:23:09', '2020-01-20 06:23:09', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (95, 'Naimushina_N', '778804441@t.me', NULL, '$2y$10$iZAq2XZ.m9dP4QdwklEI1e6bk5vL5z.Tx0dXOC71FOt6eMCh5K/96', 'Анастасия Наймушина', 'Анастасия', '+380714476719', '', 'Донецк', 1, NULL, '08.05.1999', '000', '778804441', 0, 10, 0, false, NULL, '2020-01-17 10:44:16', '2020-01-17 10:56:55', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (114, 'anatol23', '606754520@t.me', NULL, '$2y$10$p7XONB.eif2IlE9wO3Qvgem2cS/KMMokJHpfmRHxX800sNjwVx/QK', 'A L', '', NULL, '', NULL, NULL, NULL, NULL, '000', '606754520', 0, 10, 0, false, NULL, '2020-01-20 17:56:14', '2020-01-20 17:56:15', 42, 0, 0, 0, 0);
INSERT INTO users VALUES (92, 'alextotalwin', '377165508@t.me', NULL, '$2y$10$40JjhPVBTagsdR9iz76AMe4.7g9dOjQE0zjmd10Qxmm5C7f2/Rucq', 'Александр ', '', NULL, '', NULL, NULL, NULL, NULL, '000', '377165508', 0, 10, 0, false, NULL, '2020-01-12 15:30:53', '2020-01-12 15:30:53', 38, 0, 0, 0, 0);
INSERT INTO users VALUES (10, 'exxxar', '484698703@t.me', NULL, '$2y$10$Clb.bBzBe5unIKxG/HihjOKJAE52PCEvC9ohsrN4LSa6.CQ1l8iu.', 'Алексей Гукай', 'Александр', '+380714320669', '', '21312', 0, 34, '/ref', '000', '484698703', 19, 0, 156745, true, NULL, '2019-12-01 14:43:32', '2020-01-15 05:20:46', 42, 1, 0, 820, 2);
INSERT INTO users VALUES (42, 'Скидоботик', 'skidobot@gmail.com', NULL, '$2y$10$a8izy3BHsixbte7XtzCFiOomxtICqE0.HCC4zONajhAp1nQWWgk52', 'Скидоботик', '', NULL, '', NULL, NULL, NULL, NULL, '000', '1234567890', 6, 0, 0, true, NULL, '2019-12-25 11:21:36', '2020-01-20 17:56:15', 42, 1, 0, 0, 0);
INSERT INTO users VALUES (35, '997456692', '997456692@t.me', NULL, '$2y$10$6OLYizRIT9ioegvndzaeJe.H2nOkEC6rl/ckzumDUW0UVm3QoMcPq', ''' melfina o''shea ''', 'Алн', '+380714333333', '', NULL, NULL, 12, NULL, '000', '997456692', 0, 10, 600, false, NULL, '2019-12-08 19:52:51', '2019-12-08 20:41:13', 10, 1, 3, 0, 0);


--
-- Name: achievements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('achievements_id_seq', 11, true);


--
-- Name: articles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('articles_id_seq', 5, true);


--
-- Name: cashback_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('cashback_histories_id_seq', 42, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('categories_id_seq', 2, true);


--
-- Name: companies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('companies_id_seq', 6, true);


--
-- Name: events_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('events_id_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('migrations_id_seq', 25, true);


--
-- Name: prizes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('prizes_id_seq', 21, true);


--
-- Name: promocodes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('promocodes_id_seq', 11, true);


--
-- Name: promotions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('promotions_id_seq', 9, true);


--
-- Name: refferals_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('refferals_histories_id_seq', 59, true);


--
-- Name: refferals_payment_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('refferals_payment_histories_id_seq', 32, true);


--
-- Name: stats_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('stats_id_seq', 97, true);


--
-- Name: user_has_achievements_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('user_has_achievements_id_seq', 9, true);


--
-- Name: user_has_promos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('user_has_promos_id_seq', 21, true);


--
-- Name: user_in_companies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('user_in_companies_id_seq', 30, true);


--
-- Name: user_info_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('user_info_id_seq', 1, false);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: vrawpxxujbruhc
--

SELECT pg_catalog.setval('users_id_seq', 114, true);


--
-- Name: achievements achievements_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY achievements
    ADD CONSTRAINT achievements_pkey PRIMARY KEY (id);


--
-- Name: articles articles_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY articles
    ADD CONSTRAINT articles_pkey PRIMARY KEY (id);


--
-- Name: cashback_histories cashback_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY cashback_histories
    ADD CONSTRAINT cashback_histories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: companies companies_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY companies
    ADD CONSTRAINT companies_pkey PRIMARY KEY (id);


--
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: prizes prizes_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY prizes
    ADD CONSTRAINT prizes_pkey PRIMARY KEY (id);


--
-- Name: promocodes promocodes_code_unique; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY promocodes
    ADD CONSTRAINT promocodes_code_unique UNIQUE (code);


--
-- Name: promocodes promocodes_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY promocodes
    ADD CONSTRAINT promocodes_pkey PRIMARY KEY (id);


--
-- Name: promotions promotions_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY promotions
    ADD CONSTRAINT promotions_pkey PRIMARY KEY (id);


--
-- Name: refferals_histories refferals_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY refferals_histories
    ADD CONSTRAINT refferals_histories_pkey PRIMARY KEY (id);


--
-- Name: refferals_payment_histories refferals_payment_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY refferals_payment_histories
    ADD CONSTRAINT refferals_payment_histories_pkey PRIMARY KEY (id);


--
-- Name: stats stats_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY stats
    ADD CONSTRAINT stats_pkey PRIMARY KEY (id);


--
-- Name: user_has_achievements user_has_achievements_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_achievements
    ADD CONSTRAINT user_has_achievements_pkey PRIMARY KEY (id);


--
-- Name: user_has_promos user_has_promos_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_promos
    ADD CONSTRAINT user_has_promos_pkey PRIMARY KEY (id);


--
-- Name: user_in_companies user_in_companies_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_in_companies
    ADD CONSTRAINT user_in_companies_pkey PRIMARY KEY (id);


--
-- Name: user_info user_info_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_info
    ADD CONSTRAINT user_info_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_phone_unique; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_phone_unique UNIQUE (phone);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_telegram_chat_id_unique; Type: CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_telegram_chat_id_unique UNIQUE (telegram_chat_id);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: vrawpxxujbruhc
--

CREATE INDEX password_resets_email_index ON password_resets USING btree (email);


--
-- Name: cashback_histories cashback_histories_company_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY cashback_histories
    ADD CONSTRAINT cashback_histories_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies(id);


--
-- Name: cashback_histories cashback_histories_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY cashback_histories
    ADD CONSTRAINT cashback_histories_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: user_has_achievements user_has_achievements_achievement_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_achievements
    ADD CONSTRAINT user_has_achievements_achievement_id_foreign FOREIGN KEY (achievement_id) REFERENCES achievements(id);


--
-- Name: user_has_achievements user_has_achievements_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: vrawpxxujbruhc
--

ALTER TABLE ONLY user_has_achievements
    ADD CONSTRAINT user_has_achievements_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: vrawpxxujbruhc
--

REVOKE ALL ON SCHEMA public FROM postgres;
REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO vrawpxxujbruhc;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: LANGUAGE plpgsql; Type: ACL; Schema: -; Owner: postgres
--

GRANT ALL ON LANGUAGE plpgsql TO vrawpxxujbruhc;


--
-- PostgreSQL database dump complete
--

