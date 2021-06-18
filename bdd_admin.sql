--
-- PostgreSQL database dump
--

-- Dumped from database version 12.3
-- Dumped by pg_dump version 12.3

-- Started on 2021-03-15 19:07:54

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

--
-- TOC entry 5 (class 2615 OID 70756)
-- Name: admin; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA admin;


ALTER SCHEMA admin OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 204 (class 1259 OID 70765)
-- Name: privacy; Type: TABLE; Schema: admin; Owner: postgres
--

CREATE TABLE admin.privacy (
    layername character varying NOT NULL,
    userid bigint NOT NULL,
    public boolean NOT NULL
);


ALTER TABLE admin.privacy OWNER TO postgres;

--
-- TOC entry 203 (class 1259 OID 70757)
-- Name: user; Type: TABLE; Schema: admin; Owner: postgres
--

CREATE TABLE admin."user" (
    nom character varying NOT NULL,
    prenom character varying NOT NULL,
    login character varying NOT NULL,
    password character varying NOT NULL,
    userid integer NOT NULL
);


ALTER TABLE admin."user" OWNER TO postgres;

--
-- TOC entry 205 (class 1259 OID 70773)
-- Name: user_userid_seq; Type: SEQUENCE; Schema: admin; Owner: postgres
--

CREATE SEQUENCE admin.user_userid_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE admin.user_userid_seq OWNER TO postgres;

--
-- TOC entry 2833 (class 0 OID 0)
-- Dependencies: 205
-- Name: user_userid_seq; Type: SEQUENCE OWNED BY; Schema: admin; Owner: postgres
--

ALTER SEQUENCE admin.user_userid_seq OWNED BY admin."user".userid;


--
-- TOC entry 2694 (class 2604 OID 70775)
-- Name: user userid; Type: DEFAULT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY admin."user" ALTER COLUMN userid SET DEFAULT nextval('admin.user_userid_seq'::regclass);


--
-- TOC entry 2826 (class 0 OID 70765)
-- Dependencies: 204
-- Data for Name: privacy; Type: TABLE DATA; Schema: admin; Owner: postgres
--

COPY admin.privacy (layername, userid, public) FROM stdin;
\.


--
-- TOC entry 2825 (class 0 OID 70757)
-- Dependencies: 203
-- Data for Name: user; Type: TABLE DATA; Schema: admin; Owner: postgres
--

COPY admin."user" (nom, prenom, login, password, userid) FROM stdin;
\.


--
-- TOC entry 2834 (class 0 OID 0)
-- Dependencies: 205
-- Name: user_userid_seq; Type: SEQUENCE SET; Schema: admin; Owner: postgres
--

SELECT pg_catalog.setval('admin.user_userid_seq', 15, true);


--
-- TOC entry 2698 (class 2606 OID 70772)
-- Name: privacy privacy_pkey; Type: CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY admin.privacy
    ADD CONSTRAINT privacy_pkey PRIMARY KEY (layername, public);


--
-- TOC entry 2696 (class 2606 OID 70777)
-- Name: user user_pkey; Type: CONSTRAINT; Schema: admin; Owner: postgres
--

ALTER TABLE ONLY admin."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (userid);


-- Completed on 2021-03-15 19:07:54

--
-- PostgreSQL database dump complete
--

