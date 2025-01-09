--
-- PostgreSQL database dump
--

-- Dumped from database version 17.0 (Debian 17.0-1.pgdg120+1)
-- Dumped by pg_dump version 17.0 (Debian 17.0-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: audit_trigger_function(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION public.audit_trigger_function() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF (TG_OP = 'INSERT') THEN
        INSERT INTO audit_log (operation_type, table_name, record_id, new_data)
        VALUES (
            'INSERT',
            TG_TABLE_NAME,
            NEW.id,
            to_jsonb(NEW)
        );
        RETURN NEW;
    ELSIF (TG_OP = 'UPDATE') THEN
        INSERT INTO audit_log (operation_type, table_name, record_id, old_data, new_data)
        VALUES (
            'UPDATE',
            TG_TABLE_NAME,
            NEW.id,
            to_jsonb(OLD),
            to_jsonb(NEW)
        );
        RETURN NEW;
    ELSIF (TG_OP = 'DELETE') THEN
        INSERT INTO audit_log (operation_type, table_name, record_id, old_data)
        VALUES (
            'DELETE',
            TG_TABLE_NAME,
            OLD.id,
            to_jsonb(OLD)
        );
        RETURN OLD;
    END IF;
    RETURN NULL; -- To satisfy all code paths
END;
$$;


ALTER FUNCTION public.audit_trigger_function() OWNER TO postgres;

--
-- Name: albumy_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.albumy_id_seq
    START WITH 41
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.albumy_id_seq OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: albumy; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.albumy (
    id integer DEFAULT nextval('public.albumy_id_seq'::regclass) NOT NULL,
    tytul character varying(100) NOT NULL,
    data timestamp without time zone NOT NULL,
    id_uzytkownika integer NOT NULL
);


ALTER TABLE public.albumy OWNER TO postgres;

--
-- Name: audit_log; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.audit_log (
    log_id integer NOT NULL,
    operation_type character varying(10) NOT NULL,
    table_name character varying(50) NOT NULL,
    record_id integer,
    old_data jsonb,
    new_data jsonb,
    performed_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.audit_log OWNER TO postgres;

--
-- Name: audit_log_log_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.audit_log_log_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.audit_log_log_id_seq OWNER TO postgres;

--
-- Name: audit_log_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.audit_log_log_id_seq OWNED BY public.audit_log.log_id;


--
-- Name: uzytkownicy; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.uzytkownicy (
    id integer NOT NULL,
    login character varying(16) NOT NULL,
    haslo character varying(32) NOT NULL,
    email character varying(128) NOT NULL,
    zarejestrowany date NOT NULL,
    uprawnienia character varying(30) DEFAULT 'u┼╝ytkownik'::character varying NOT NULL,
    aktywny smallint NOT NULL,
    CONSTRAINT uzytkownicy_uprawnienia_check CHECK (((uprawnienia)::text = ANY ((ARRAY['u┼╝ytkownik'::character varying, 'moderator'::character varying, 'administrator'::character varying])::text[])))
);


ALTER TABLE public.uzytkownicy OWNER TO postgres;

--
-- Name: zdjecia_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zdjecia_id_seq
    START WITH 97
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zdjecia_id_seq OWNER TO postgres;

--
-- Name: zdjecia; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zdjecia (
    id integer DEFAULT nextval('public.zdjecia_id_seq'::regclass) NOT NULL,
    opis character varying(255) NOT NULL,
    id_albumu integer NOT NULL,
    data timestamp without time zone NOT NULL,
    zaakceptowane smallint NOT NULL,
    opiszdjecia character varying(255) NOT NULL
);


ALTER TABLE public.zdjecia OWNER TO postgres;

--
-- Name: photodetailsview; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.photodetailsview AS
 SELECT z.id AS photo_id,
    z.opiszdjecia AS photo_name,
    a.tytul AS album_title,
    u.login AS user_login,
    z.zaakceptowane AS is_accepted
   FROM ((public.zdjecia z
     JOIN public.albumy a ON ((z.id_albumu = a.id)))
     JOIN public.uzytkownicy u ON ((a.id_uzytkownika = u.id)));


ALTER VIEW public.photodetailsview OWNER TO postgres;

--
-- Name: profil; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.profil (
    id integer NOT NULL,
    bio text,
    avatar_url character varying(255)
);


ALTER TABLE public.profil OWNER TO postgres;

--
-- Name: tagi; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tagi (
    id integer NOT NULL,
    nazwa character varying(50) NOT NULL
);


ALTER TABLE public.tagi OWNER TO postgres;

--
-- Name: tagi_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.tagi_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.tagi_id_seq OWNER TO postgres;

--
-- Name: tagi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.tagi_id_seq OWNED BY public.tagi.id;


--
-- Name: zdjecia_komentarze_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zdjecia_komentarze_id_seq
    START WITH 10
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zdjecia_komentarze_id_seq OWNER TO postgres;

--
-- Name: zdjecia_komentarze; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zdjecia_komentarze (
    id integer DEFAULT nextval('public.zdjecia_komentarze_id_seq'::regclass) NOT NULL,
    id_zdjecia integer NOT NULL,
    id_uzytkownika integer NOT NULL,
    data timestamp without time zone NOT NULL,
    komentarz text NOT NULL,
    zaakceptowany smallint NOT NULL
);


ALTER TABLE public.zdjecia_komentarze OWNER TO postgres;

--
-- Name: zdjecia_oceny_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.zdjecia_oceny_id_seq
    START WITH 26
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.zdjecia_oceny_id_seq OWNER TO postgres;

--
-- Name: zdjecia_oceny; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zdjecia_oceny (
    id integer DEFAULT nextval('public.zdjecia_oceny_id_seq'::regclass) NOT NULL,
    id_zdjecia integer NOT NULL,
    id_uzytkownika integer NOT NULL,
    ocena smallint NOT NULL
);


ALTER TABLE public.zdjecia_oceny OWNER TO postgres;

--
-- Name: useractivityview; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.useractivityview AS
 SELECT u.id AS user_id,
    u.login AS user_login,
    count(DISTINCT a.id) AS album_count,
    count(DISTINCT z.id) AS photo_count,
    count(DISTINCT kc.id) AS comment_count,
    count(DISTINCT zo.id) AS rating_count
   FROM ((((public.uzytkownicy u
     LEFT JOIN public.albumy a ON ((u.id = a.id_uzytkownika)))
     LEFT JOIN public.zdjecia z ON ((a.id = z.id_albumu)))
     LEFT JOIN public.zdjecia_komentarze kc ON ((u.id = kc.id_uzytkownika)))
     LEFT JOIN public.zdjecia_oceny zo ON ((u.id = zo.id_uzytkownika)))
  GROUP BY u.id, u.login;


ALTER VIEW public.useractivityview OWNER TO postgres;

--
-- Name: uzytkownicy_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.uzytkownicy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.uzytkownicy_id_seq OWNER TO postgres;

--
-- Name: uzytkownicy_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.uzytkownicy_id_seq OWNED BY public.uzytkownicy.id;


--
-- Name: zdjecia_tag; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.zdjecia_tag (
    zdjecia_id integer NOT NULL,
    tagi_id integer NOT NULL
);


ALTER TABLE public.zdjecia_tag OWNER TO postgres;

--
-- Name: audit_log log_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_log ALTER COLUMN log_id SET DEFAULT nextval('public.audit_log_log_id_seq'::regclass);


--
-- Name: tagi id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagi ALTER COLUMN id SET DEFAULT nextval('public.tagi_id_seq'::regclass);


--
-- Name: uzytkownicy id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.uzytkownicy ALTER COLUMN id SET DEFAULT nextval('public.uzytkownicy_id_seq'::regclass);


--
-- Data for Name: albumy; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.albumy (id, tytul, data, id_uzytkownika) FROM stdin;
1	Abstrakcja	2021-12-04 20:06:38	1
2	Kwiaty	2021-12-04 21:30:19	3
3	klocki	2021-12-04 22:04:20	4
4	Konkurs	2021-12-05 18:41:54	4
5	Moj album	2021-12-05 20:04:12	1
6	probny	2021-12-05 20:44:05	5
7	tablice	2021-12-05 20:46:26	7
8	obrazki	2021-12-05 20:48:49	9
9	dom z papieru	2021-12-05 20:50:28	8
10	pole	2021-12-05 20:52:23	7
11	Widoki z lotu ptaka	2021-12-05 20:53:27	4
12	Gra	2021-12-05 20:54:18	1
13	Termobag	2021-12-05 20:55:58	5
15	Mapa Chinrn	2021-12-05 20:58:21	3
16	Fifa Karty	2021-12-05 20:59:48	1
17	Rakieta	2021-12-05 21:01:04	6
19	Nagrobki	2021-12-05 21:04:12	7
20	Cristiano Ronaldo	2021-12-05 21:05:56	9
21	Statua Wolno┼Ťci	2021-12-05 21:07:06	4
22	Dreamlight Logos	2021-12-05 21:08:52	1
25	M'Orwell	2022-01-03 14:47:57	21
28	M'Orwell'Kubalonka	2022-01-03 15:23:09	21
29	Wis┼éa	2022-01-07 21:15:21	22
30	WislaDwa	2022-01-07 21:42:30	22
31	Wis┼éa3	2022-01-07 23:00:14	22
32	K'ubalonka	2022-01-07 23:13:26	22
33	Album Szkolny	2022-01-13 22:27:17	24
34	Szpital	2022-01-13 22:28:16	24
35	Szpitaldwa	2022-01-13 22:32:07	24
36	Zdj─Öcia kotk├│w	2022-01-17 11:04:41	25
37	Wisla	2022-01-17 11:19:13	22
39	Zdj─Öcia kotk├│w	2022-01-17 11:31:18	25
40	Zdj─Öcia kotk├│w	2022-01-17 11:32:08	25
42	Okladki	2024-12-26 15:17:50	28
43	TestowyAlbum1	2024-12-26 15:36:36	28
44	testowy album 2	2024-12-26 15:36:52	28
46	testowy album 4	2024-12-26 15:37:19	28
47	ijsudhijd[sok	2024-12-27 18:20:42	28
49	Zdjecia Robak├│w	2024-12-27 18:28:20	28
48	bez tytulu	2024-12-27 18:20:54	28
41	Footballers	2024-11-27 20:36:27	27
\.


--
-- Data for Name: audit_log; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.audit_log (log_id, operation_type, table_name, record_id, old_data, new_data, performed_at) FROM stdin;
1	UPDATE	zdjecia	85	{"id": 85, "data": "2022-01-10T11:10:16", "opis": "unnamed.jpg", "id_albumu": 29, "opiszdjecia": "one more night", "zaakceptowane": 0}	{"id": 85, "data": "2022-01-10T11:10:16", "opis": "unnamed.jpg", "id_albumu": 29, "opiszdjecia": "one more night", "zaakceptowane": 1}	2025-01-08 22:19:39.456571
2	DELETE	zdjecia	78	{"id": 78, "data": "2022-01-09T20:13:31", "opis": "269695056_600732711021166_1524724071430010214_n_Easy-Resize.com.jpg", "id_albumu": 29, "opiszdjecia": "guaranteed", "zaakceptowane": 1}	\N	2025-01-08 22:22:22.0147
3	INSERT	uzytkownicy	29	\N	{"id": 29, "email": "konto@gmail.com", "haslo": "3ed705121639f43034b8be434cdc9ae3", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	2025-01-08 22:23:36.060137
4	INSERT	albumy	50	\N	{"id": 50, "data": "2025-01-08T23:10:53", "tytul": "the night we met", "id_uzytkownika": 29}	2025-01-08 23:10:53.320771
5	INSERT	zdjecia	109	\N	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Logo albumu", "zaakceptowane": 0}	2025-01-08 23:11:17.438293
6	INSERT	albumy	51	\N	{"id": 51, "data": "2025-01-08T23:11:43", "tytul": "Feel Something", "id_uzytkownika": 29}	2025-01-08 23:11:43.115773
7	INSERT	zdjecia	110	\N	{"id": 110, "data": "2025-01-08T23:12:04", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye trzymajacy statuetk─Ö", "zaakceptowane": 0}	2025-01-08 23:12:04.641767
8	UPDATE	uzytkownicy	29	{"id": 29, "email": "konto@gmail.com", "haslo": "3ed705121639f43034b8be434cdc9ae3", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "3ed705121639f43034b8be434cdc9ae3", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	2025-01-08 23:13:32.865761
9	UPDATE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "3ed705121639f43034b8be434cdc9ae3", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	2025-01-08 23:58:08.835038
10	UPDATE	albumy	51	{"id": 51, "data": "2025-01-08T23:11:43", "tytul": "Feel Something", "id_uzytkownika": 29}	{"id": 51, "data": "2025-01-08T23:11:43", "tytul": "Feel something", "id_uzytkownika": 29}	2025-01-08 23:58:24.422627
11	UPDATE	albumy	50	{"id": 50, "data": "2025-01-08T23:10:53", "tytul": "the night we met", "id_uzytkownika": 29}	{"id": 50, "data": "2025-01-08T23:10:53", "tytul": "The night we met", "id_uzytkownika": 29}	2025-01-08 23:58:33.276147
12	UPDATE	zdjecia	109	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Logo albumu", "zaakceptowane": 0}	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze zdj─Öcie albumu", "zaakceptowane": 0}	2025-01-09 13:41:20.820132
13	UPDATE	zdjecia	109	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze zdj─Öcie albumu", "zaakceptowane": 0}	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Logo Albumu", "zaakceptowane": 0}	2025-01-09 13:42:59.735809
14	UPDATE	zdjecia	109	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Logo Albumu", "zaakceptowane": 0}	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze", "zaakceptowane": 0}	2025-01-09 13:43:13.805799
15	UPDATE	zdjecia	109	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze", "zaakceptowane": 0}	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze zdj─Öcie albumu", "zaakceptowane": 0}	2025-01-09 13:46:08.142438
16	DELETE	zdjecia	109	{"id": 109, "data": "2025-01-08T23:11:17", "opis": "Smart Match (1).png", "id_albumu": 50, "opiszdjecia": "Pierwsze zdj─Öcie albumu", "zaakceptowane": 0}	\N	2025-01-09 13:57:53.567227
17	DELETE	zdjecia	110	{"id": 110, "data": "2025-01-08T23:12:04", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye trzymajacy statuetk─Ö", "zaakceptowane": 0}	\N	2025-01-09 14:19:19.101612
18	INSERT	zdjecia	111	\N	{"id": 111, "data": "2025-01-09T14:19:43", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye with award", "zaakceptowane": 0}	2025-01-09 14:19:43.106044
19	DELETE	zdjecia	94	{"id": 94, "data": "2022-01-13T22:32:24", "opis": "pobrane.jpg", "id_albumu": 35, "opiszdjecia": "zmar┼éy", "zaakceptowane": 0}	\N	2025-01-09 14:40:12.079011
20	DELETE	zdjecia	86	{"id": 86, "data": "2022-01-10T11:10:55", "opis": "1.jpg", "id_albumu": 29, "opiszdjecia": "wie┼╝a Eifla", "zaakceptowane": 0}	\N	2025-01-09 14:40:15.571916
21	DELETE	zdjecia	87	{"id": 87, "data": "2022-01-10T11:11:15", "opis": "Flower_(166180281).jpeg", "id_albumu": 29, "opiszdjecia": "czerwony kwiatek", "zaakceptowane": 0}	\N	2025-01-09 14:40:17.285533
22	DELETE	zdjecia	88	{"id": 88, "data": "2022-01-10T11:11:25", "opis": "pobrane.jpeg", "id_albumu": 29, "opiszdjecia": "Pole kwiatowe", "zaakceptowane": 0}	\N	2025-01-09 14:40:18.924772
23	DELETE	zdjecia	89	{"id": 89, "data": "2022-01-10T11:13:58", "opis": "IMG_20200927_175100.jpg", "id_albumu": 29, "opiszdjecia": "rakiety", "zaakceptowane": 0}	\N	2025-01-09 14:40:21.033096
24	DELETE	zdjecia	91	{"id": 91, "data": "2022-01-10T11:22:25", "opis": "creative2.jpg", "id_albumu": 29, "opiszdjecia": "glosniki2", "zaakceptowane": 0}	\N	2025-01-09 14:40:22.801521
25	UPDATE	zdjecia	111	{"id": 111, "data": "2025-01-09T14:19:43", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye with award", "zaakceptowane": 0}	{"id": 111, "data": "2025-01-09T14:19:43", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye with award", "zaakceptowane": 1}	2025-01-09 14:40:26.459981
26	UPDATE	uzytkownicy	1	{"id": 1, "email": "nevvkeofficial@gamil.com", "haslo": "ae22425e479969568d3c3aa3ed0a91ed", "login": "nevkeofficial", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2021-11-28"}	{"id": 1, "email": "nevvkeofficial@gamil.com", "haslo": "ae22425e479969568d3c3aa3ed0a91ed", "login": "nevkeofficial", "aktywny": 0, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2021-11-28"}	2025-01-09 15:39:03.762784
27	UPDATE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 0, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	2025-01-09 15:41:26.631953
28	UPDATE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 0, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	2025-01-09 15:42:14.237816
29	UPDATE	uzytkownicy	1	{"id": 1, "email": "nevvkeofficial@gamil.com", "haslo": "ae22425e479969568d3c3aa3ed0a91ed", "login": "nevkeofficial", "aktywny": 0, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2021-11-28"}	{"id": 1, "email": "nevvkeofficial@gamil.com", "haslo": "ae22425e479969568d3c3aa3ed0a91ed", "login": "nevkeofficial", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2021-11-28"}	2025-01-09 15:42:17.1847
30	DELETE	zdjecia	25	{"id": 25, "data": "2021-12-05T20:57:10", "opis": "SorryEmoji.jpg", "id_albumu": 14, "opiszdjecia": "", "zaakceptowane": 1}	\N	2025-01-09 15:43:40.505446
31	DELETE	zdjecia	26	{"id": 26, "data": "2021-12-05T20:57:23", "opis": "tag emoji 9.png", "id_albumu": 14, "opiszdjecia": "", "zaakceptowane": 1}	\N	2025-01-09 15:43:40.505446
32	DELETE	zdjecia	27	{"id": 27, "data": "2021-12-05T20:57:34", "opis": "thinking-face.png", "id_albumu": 14, "opiszdjecia": "", "zaakceptowane": 1}	\N	2025-01-09 15:43:40.505446
33	DELETE	zdjecia	35	{"id": 35, "data": "2021-12-05T21:03:04", "opis": "creative.jpeg", "id_albumu": 18, "opiszdjecia": "", "zaakceptowane": 1}	\N	2025-01-09 15:43:40.505446
34	DELETE	zdjecia	36	{"id": 36, "data": "2021-12-05T21:03:26", "opis": "creative2.jpg", "id_albumu": 18, "opiszdjecia": "", "zaakceptowane": 1}	\N	2025-01-09 15:43:40.505446
35	DELETE	albumy	14	{"id": 14, "data": "2021-12-05T20:56:36", "tytul": "Emoji", "id_uzytkownika": 2}	\N	2025-01-09 15:43:40.505446
36	DELETE	albumy	18	{"id": 18, "data": "2021-12-05T21:02:52", "tytul": "G┼éo┼Ťniki", "id_uzytkownika": 2}	\N	2025-01-09 15:43:40.505446
37	DELETE	uzytkownicy	2	{"id": 2, "email": "bartoszek@gmail.com", "haslo": "cd104fe43e8b111626eafec3b3f707f6", "login": "bartoszek", "aktywny": 0, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2021-12-02"}	\N	2025-01-09 15:43:40.505446
38	UPDATE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "u┼╝ytkownik", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "administrator", "zarejestrowany": "2025-01-08"}	2025-01-09 15:51:16.532843
39	UPDATE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "administrator", "zarejestrowany": "2025-01-08"}	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "moderator", "zarejestrowany": "2025-01-08"}	2025-01-09 15:51:23.517061
40	UPDATE	zdjecia	103	{"id": 103, "data": "2024-12-26T15:36:47", "opis": "C0002.00_29_37_14.Still002.jpg", "id_albumu": 43, "opiszdjecia": "pk", "zaakceptowane": 0}	{"id": 103, "data": "2024-12-26T15:36:47", "opis": "C0002.00_29_37_14.Still002.jpg", "id_albumu": 43, "opiszdjecia": "pk", "zaakceptowane": 1}	2025-01-09 15:52:26.015859
41	DELETE	zdjecia	12	{"id": 12, "data": "2021-12-05T20:45:54", "opis": "probny2.jpg", "id_albumu": 6, "opiszdjecia": "", "zaakceptowane": 0}	\N	2025-01-09 15:53:39.827596
42	DELETE	zdjecia	111	{"id": 111, "data": "2025-01-09T14:19:43", "opis": "gotye-now-2023-what-happened-somebody-that-i-used-know-hitmaker-how-much-he-earned-hit-song.jpg", "id_albumu": 51, "opiszdjecia": "gotye with award", "zaakceptowane": 1}	\N	2025-01-09 15:55:42.045097
43	DELETE	albumy	51	{"id": 51, "data": "2025-01-08T23:11:43", "tytul": "Feel something", "id_uzytkownika": 29}	\N	2025-01-09 15:55:42.045097
44	DELETE	albumy	50	{"id": 50, "data": "2025-01-08T23:10:53", "tytul": "The night we met", "id_uzytkownika": 29}	\N	2025-01-09 15:55:42.045097
45	DELETE	uzytkownicy	29	{"id": 29, "email": "kontomoje@gmail.com", "haslo": "9cb221782ae2b5759f912da2ee633221", "login": "kontodousuniecia", "aktywny": 1, "uprawnienia": "moderator", "zarejestrowany": "2025-01-08"}	\N	2025-01-09 15:55:42.045097
\.


--
-- Data for Name: profil; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.profil (id, bio, avatar_url) FROM stdin;
\.


--
-- Data for Name: tagi; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tagi (id, nazwa) FROM stdin;
\.


--
-- Data for Name: uzytkownicy; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.uzytkownicy (id, login, haslo, email, zarejestrowany, uprawnienia, aktywny) FROM stdin;
3	bartoszek2	cd104fe43e8b111626eafec3b3f707f6	bartoszek2@gmail.com	2021-12-02	u┼╝ytkownik	1
4	Kazimierz	f5cc245dd37bbc84fd405802725a7519	kazimierz@gmail.com	2021-12-02	u┼╝ytkownik	1
5	karoleka	281180d76e3d5bd7e4bfb7b713db1849	karoleka1@gmail.com	2021-12-03	u┼╝ytkownik	1
6	administrator	2701ab6335f77c2fcba0aa9480e2de9b	admin@gmail.com	2021-12-03	administrator	1
7	tomaszkacper	2eedb980f2ae7577aac2f52e795c9f86	tomasz@gmail.com	2021-12-05	u┼╝ytkownik	1
8	kacperek	06e7c8f9f0edd9034f4b01d83ffad92b	jutuberzy2@gmail.com	2021-12-05	u┼╝ytkownik	1
9	qwertyuiop	88ca80df2d72dbebcef83a756c323da1	qwerty@gmail.com	2021-12-05	u┼╝ytkownik	1
10	gwizdalke	8d101f40d882b7c2a733e5d72d2b4df4	gwizdalke@gmail.com	2021-12-06	u┼╝ytkownik	1
12	nauczyciel2	ef561c79342ff86785c280a175b82faa	jutuberzy@gmail.com	2021-12-09	u┼╝ytkownik	1
13	aqueelmusic	958ae1a55b67f4577194ad8ae95df82a	bartekszczygiel5@gmail.com	2022-01-02	u┼╝ytkownik	1
14	zxcvbnml	b3bbaf7c3b1709ed3f44d2a29f5f2dbf	malpakasia@gmail.com	2022-01-02	u┼╝ytkownik	1
15	zxcvbnmli	b3bbaf7c3b1709ed3f44d2a29f5f2dbf	malpakasia@gmail.com	2022-01-02	u┼╝ytkownik	1
16	zxcvbnmlisz	b3bbaf7c3b1709ed3f44d2a29f5f2dbf	malpakasia@gmail.com	2022-01-02	u┼╝ytkownik	1
17	zxcvbnmliszkk	b3bbaf7c3b1709ed3f44d2a29f5f2dbf	malpakasia@gmail.com	2022-01-02	u┼╝ytkownik	1
18	qwertyuiop1	88ca80df2d72dbebcef83a756c323da1	jutuberzy2@gmail.com	2022-01-02	u┼╝ytkownik	1
19	qwertyuiop12	88ca80df2d72dbebcef83a756c323da1	jutuberzy2@gmail.com	2022-01-02	u┼╝ytkownik	1
20	qwertyuiop122	88ca80df2d72dbebcef83a756c323da1	jutuberzy2@gmail.com	2022-01-02	u┼╝ytkownik	1
21	testowyuser	ab0c8572a189779862f438df7b604123	qwerty@gmail.com	2022-01-03	u┼╝ytkownik	1
24	uczenbrzezinski	96fcd2e8201300092d6e103735511cc0	szkola@gmail.com	2022-01-13	u┼╝ytkownik	1
25	erykkleryk	35467274493e51e2e8ad74fac89f4e89	ghaster@gmail.com	2022-01-17	u┼╝ytkownik	1
22	wislauser	207023ccb44feb4d7dadca005ce29a64	wisla@op.pl	2022-01-07	u┼╝ytkownik	1
26	wdpaitest	a7cabaeccd03442ed10c98e1edac2899	jsjs@gmail.com	2024-11-27	u┼╝ytkownik	1
27	wdpaitest2	a7cabaeccd03442ed10c98e1edac2899	jsjs@gmail.com	2024-11-27	u┼╝ytkownik	1
28	SzymonDral	df88e488da483a7b777ed07f0d9e2d65	szymon@gmail.com	2024-12-04	administrator	1
1	nevkeofficial	ae22425e479969568d3c3aa3ed0a91ed	nevvkeofficial@gamil.com	2021-11-28	u┼╝ytkownik	1
\.


--
-- Data for Name: zdjecia; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.zdjecia (id, opis, id_albumu, data, zaakceptowane, opiszdjecia) FROM stdin;
1	logo1.png	1	2021-12-04 20:07:05	1	
2	logo2.png	1	2021-12-04 20:09:48	1	
3	logo3.png	1	2021-12-04 20:09:59	1	
4	logo4.png	1	2021-12-04 20:10:10	1	
5	kwiat1.jpg	2	2021-12-04 21:30:38	1	
6	klocki1.jpg	3	2021-12-04 22:04:30	0	
7	konkurs1.jpg	4	2021-12-05 18:42:51	1	
8	konkurs2.jpg	4	2021-12-05 18:43:05	1	
9	zdj1.jpg	5	2021-12-05 20:04:32	1	
10	zdj2.jpg	5	2021-12-05 20:04:53	1	
11	probny1.jpg	6	2021-12-05 20:44:29	1	
13	tablice1.png	7	2021-12-05 20:47:23	1	
14	tablice2.png	7	2021-12-05 20:47:33	1	
15	obrazek1.jpg	8	2021-12-05 20:49:38	1	
16	obrazek2.jpg	8	2021-12-05 20:49:53	1	
17	dom z papieru.jpg	9	2021-12-05 20:50:58	1	
18	pole.jpg	10	2021-12-05 20:52:51	1	
19	1.jpg	11	2021-12-05 20:53:55	1	
20	2.jpg	11	2021-12-05 20:54:05	1	
21	bosak.png	12	2021-12-05 20:54:57	1	
22	bosak2.png	12	2021-12-05 20:55:07	1	
23	torba.jpg	13	2021-12-05 20:56:13	1	
24	torba2.jpg	13	2021-12-05 20:56:24	1	
28	chiny1.jpg	15	2021-12-05 20:58:32	1	
29	chiny2.png	15	2021-12-05 20:58:44	1	
30	Toty2014.png	16	2021-12-05 21:00:20	1	
31	Toty2015.jpg	16	2021-12-05 21:00:33	1	
32	IMG_20200927_175100.jpg	17	2021-12-05 21:01:46	1	
33	IMG_20200927_175112.jpg	17	2021-12-05 21:01:58	1	
34	IMG_20200927_175119.jpg	17	2021-12-05 21:02:11	1	
37	r-i-p-gr├│b-kamie┼ä-34707618.jpg	19	2021-12-05 21:04:31	1	
38	cr7-1.png	20	2021-12-05 21:06:10	1	
39	Cristiano_Ronaldo_Euro_2016.jpg	20	2021-12-05 21:06:09	1	
40	USA.jpg	21	2021-12-05 21:08:29	1	
41	117949201_3491900074188437_6045014161093993081_n.png	22	2021-12-05 21:09:55	1	
42	117973613_1010173219419582_6709405740551839275_n.png	22	2021-12-05 21:10:14	1	
43	121966943_677016526525903_6930373371625284970_n.jpg	22	2021-12-05 21:10:15	1	
44	gory.jpg	29	2022-01-08 21:54:50	1	G├│ry
45	Bez┬átytu┼éu.png	29	2022-01-08 23:09:47	1	amongus
70	black.png	29	2022-01-09 19:34:16	1	amongus
74	straz miejska.jpg	29	2022-01-09 19:50:12	1	stra┼╝
76	spacja.png	29	2022-01-09 20:07:45	1	spacja baner
77	tenor.gif	29	2022-01-09 20:12:20	1	animacja zabijania
79	270113841_622911998960199_1282227184844474626_n.jpg	29	2022-01-09 20:13:53	1	francuski 
80	pexels-pixabay-290470.jpg	29	2022-01-09 20:14:05	1	t┼éo1
81	pexels-janez-podnar-1424246.jpg	29	2022-01-09 20:14:15	1	t┼éo2
82	236872158_877903736455370_1820521426227787126_n.jpg	29	2022-01-09 20:14:28	1	keithian
83	pexels-luis-quintero-2471235 (1).jpg	29	2022-01-09 20:14:48	1	t┼éologa
84	pexels-photo-1555900.jpeg	29	2022-01-09 20:14:57	1	t┼éo3
90	creative.jpeg	29	2022-01-10 11:22:12	0	g┼éo┼Ťniki
92	Toty2014.png	29	2022-01-10 11:22:39	0	karty
93	original_prepared_photo4_Moment.jpg	33	2022-01-13 22:27:48	0	pok├│j muzyczny
95	215270485_6088569947849665_5700937751452643882_n.jpg	35	2022-01-13 23:33:50	0	pamorama
96	pobrane.jpg	36	2022-01-17 11:05:22	1	Fotograf
101	462550702_2002841046848945_2444174880026040290_n.jpg	41	2024-11-27 21:22:53	1	tablica
102	10.jpg	42	2024-12-26 15:20:09	0	Nasz Zesp├│┼é
104	jpg-czarne.jpg	44	2024-12-26 15:37:00	0	tlop
106	jpg2jpg.jpg	46	2024-12-26 15:37:28	0	wearaesr
107	3.png	49	2024-12-27 18:33:35	0	okladka
108	11.png	49	2024-12-27 19:05:58	1	11
85	unnamed.jpg	29	2022-01-10 11:10:16	1	one more night
103	C0002.00_29_37_14.Still002.jpg	43	2024-12-26 15:36:47	1	pk
\.


--
-- Data for Name: zdjecia_komentarze; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.zdjecia_komentarze (id, id_zdjecia, id_uzytkownika, data, komentarz, zaakceptowany) FROM stdin;
1	1	22	2022-01-13 08:46:00	Super Grafika , autor wykona┼é kawa┼é dobrej roboty :D	1
3	1	22	2022-01-13 13:55:14	AAAAAAAAAA─ć─ć─ć─ć	0
5	82	11	2022-01-13 14:05:32	Dobry piosenkarz	0
6	37	22	2022-01-13 22:57:50	S┼éabo, ┼╝e kto┼Ť umar┼é	0
7	1	25	2022-01-17 11:03:54	Ale super zdjecie	0
8	96	25	2022-01-17 11:06:27	Koxrn	0
9	3	22	2022-01-24 12:21:26	Siemka	0
10	101	27	2024-12-04 19:28:25	10/10	1
2	1	22	2022-01-13 13:52:39	Naprawde dobra robota	1
11	4	28	2025-01-03 21:19:21.664412	Nie podoba mi si─Ö ta buzia	1
\.


--
-- Data for Name: zdjecia_oceny; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.zdjecia_oceny (id, id_zdjecia, id_uzytkownika, ocena) FROM stdin;
8	4	22	7
14	83	22	10
15	84	22	5
16	17	22	10
17	1	22	10
18	45	22	7
19	2	22	7
20	82	11	9
21	37	22	3
22	1	25	8
23	96	25	10
24	5	22	8
25	3	22	7
26	101	27	10
\.


--
-- Data for Name: zdjecia_tag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.zdjecia_tag (zdjecia_id, tagi_id) FROM stdin;
\.


--
-- Name: albumy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.albumy_id_seq', 51, true);


--
-- Name: audit_log_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.audit_log_log_id_seq', 45, true);


--
-- Name: tagi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.tagi_id_seq', 1, false);


--
-- Name: uzytkownicy_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.uzytkownicy_id_seq', 29, true);


--
-- Name: zdjecia_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zdjecia_id_seq', 111, true);


--
-- Name: zdjecia_komentarze_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zdjecia_komentarze_id_seq', 13, true);


--
-- Name: zdjecia_oceny_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.zdjecia_oceny_id_seq', 34, true);


--
-- Name: profil Profil_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profil
    ADD CONSTRAINT "Profil_pkey" PRIMARY KEY (id);


--
-- Name: albumy albumy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.albumy
    ADD CONSTRAINT albumy_pkey PRIMARY KEY (id);


--
-- Name: audit_log audit_log_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.audit_log
    ADD CONSTRAINT audit_log_pkey PRIMARY KEY (log_id);


--
-- Name: tagi nazwa_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagi
    ADD CONSTRAINT nazwa_unique UNIQUE (nazwa);


--
-- Name: tagi tagi_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tagi
    ADD CONSTRAINT tagi_pkey PRIMARY KEY (id);


--
-- Name: zdjecia_oceny unique_photo_user_rating; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_oceny
    ADD CONSTRAINT unique_photo_user_rating UNIQUE (id_zdjecia, id_uzytkownika);


--
-- Name: uzytkownicy uzytkownicy_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.uzytkownicy
    ADD CONSTRAINT uzytkownicy_pkey PRIMARY KEY (id);


--
-- Name: zdjecia_komentarze zdjecia_komentarze_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_komentarze
    ADD CONSTRAINT zdjecia_komentarze_pkey PRIMARY KEY (id);


--
-- Name: zdjecia_oceny zdjecia_oceny_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_oceny
    ADD CONSTRAINT zdjecia_oceny_pkey PRIMARY KEY (id);


--
-- Name: zdjecia zdjecia_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia
    ADD CONSTRAINT zdjecia_pkey PRIMARY KEY (id);


--
-- Name: zdjecia_tag zdjecia_tag_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_tag
    ADD CONSTRAINT zdjecia_tag_pkey PRIMARY KEY (zdjecia_id, tagi_id);


--
-- Name: fki_f_id_albumu; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_f_id_albumu ON public.zdjecia USING btree (id_albumu);


--
-- Name: fki_f_id_uzytkownika; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_f_id_uzytkownika ON public.zdjecia_oceny USING btree (id_uzytkownika);


--
-- Name: fki_f_tagi; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_f_tagi ON public.zdjecia_tag USING btree (tagi_id);


--
-- Name: fki_f_zdjecia_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_f_zdjecia_id ON public.zdjecia_tag USING btree (zdjecia_id);


--
-- Name: fki_fk_id_uzytkownicy; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_fk_id_uzytkownicy ON public.zdjecia_komentarze USING btree (id_uzytkownika);


--
-- Name: fki_fk_id_zdjecia; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_fk_id_zdjecia ON public.zdjecia_komentarze USING btree (id_zdjecia);


--
-- Name: fki_i; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX fki_i ON public.zdjecia_oceny USING btree (id_zdjecia);


--
-- Name: albumy trg_audit_albumy; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_audit_albumy AFTER INSERT OR DELETE OR UPDATE ON public.albumy FOR EACH ROW EXECUTE FUNCTION public.audit_trigger_function();


--
-- Name: uzytkownicy trg_audit_uzytkownicy; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_audit_uzytkownicy AFTER INSERT OR DELETE OR UPDATE ON public.uzytkownicy FOR EACH ROW EXECUTE FUNCTION public.audit_trigger_function();


--
-- Name: zdjecia trg_audit_zdjecia; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trg_audit_zdjecia AFTER INSERT OR DELETE OR UPDATE ON public.zdjecia FOR EACH ROW EXECUTE FUNCTION public.audit_trigger_function();


--
-- Name: zdjecia f_id_albumu; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia
    ADD CONSTRAINT f_id_albumu FOREIGN KEY (id_albumu) REFERENCES public.albumy(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- Name: zdjecia_oceny f_id_uzytkownika; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_oceny
    ADD CONSTRAINT f_id_uzytkownika FOREIGN KEY (id_uzytkownika) REFERENCES public.uzytkownicy(id) ON UPDATE CASCADE ON DELETE SET NULL NOT VALID;


--
-- Name: zdjecia_oceny f_id_zdjecia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_oceny
    ADD CONSTRAINT f_id_zdjecia FOREIGN KEY (id_zdjecia) REFERENCES public.zdjecia(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- Name: zdjecia_tag f_tagi; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_tag
    ADD CONSTRAINT f_tagi FOREIGN KEY (tagi_id) REFERENCES public.tagi(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- Name: zdjecia_tag f_zdjecia_id; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_tag
    ADD CONSTRAINT f_zdjecia_id FOREIGN KEY (zdjecia_id) REFERENCES public.zdjecia(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- Name: zdjecia_komentarze fk_id_uzytkownicy; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_komentarze
    ADD CONSTRAINT fk_id_uzytkownicy FOREIGN KEY (id_uzytkownika) REFERENCES public.uzytkownicy(id) ON UPDATE CASCADE ON DELETE SET NULL NOT VALID;


--
-- Name: zdjecia_komentarze fk_id_zdjecia; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.zdjecia_komentarze
    ADD CONSTRAINT fk_id_zdjecia FOREIGN KEY (id_zdjecia) REFERENCES public.zdjecia(id) ON UPDATE CASCADE ON DELETE CASCADE NOT VALID;


--
-- Name: profil fp_id_uzytkownik; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.profil
    ADD CONSTRAINT fp_id_uzytkownik FOREIGN KEY (id) REFERENCES public.uzytkownicy(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

