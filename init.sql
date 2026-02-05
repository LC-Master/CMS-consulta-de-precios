CREATE TABLE Store (
    [ID] INT,
    [Name] NVARCHAR(255),
    [StoreCode] NVARCHAR(50),
    [Region] NVARCHAR(255),
    [Address1] NVARCHAR(MAX),
    [Address2] NVARCHAR(MAX),
    [City] NVARCHAR(100),
    [Country] NVARCHAR(100),
    [FaxNumber] NVARCHAR(50),
    [PhoneNumber] NVARCHAR(50),
    [State] NVARCHAR(100),
    [Zip] NVARCHAR(50),
    [ParentStoreID] INT,
    [ScheduleHourMask1] INT,
    [ScheduleHourMask2] INT,
    [ScheduleHourMask3] INT,
    [ScheduleHourMask4] INT,
    [ScheduleHourMask5] INT,
    [ScheduleHourMask6] INT,
    [ScheduleHourMask7] INT,
    [ScheduleMinute] INT,
    [RetryCount] INT,
    [RetryDelay] INT,
    [LastUpdated] DATETIME,
    [DBTimeStamp] NVARCHAR(50),
    [AccountName] NVARCHAR(255),
    [Password] NVARCHAR(255),
    [AutoID] INT PRIMARY KEY,
    [Inactive] BIT,
    [SyncedStoreStatus] BIT,
    [SyncGuid] UNIQUEIDENTIFIER,
    [StoreKey] NVARCHAR(MAX)
);

INSERT INTO Store (ID, Name, StoreCode, Region, Address1, Address2, City, Country, FaxNumber, PhoneNumber, State, Zip, ParentStoreID, ScheduleHourMask1, ScheduleHourMask2, ScheduleHourMask3, ScheduleHourMask4, ScheduleHourMask5, ScheduleHourMask6, ScheduleHourMask7, ScheduleMinute, RetryCount, RetryDelay, LastUpdated, DBTimeStamp, AccountName, Password, AutoID, Inactive, SyncedStoreStatus, SyncGuid, StoreKey)
VALUES
(201, 'Farmacia BOLEITA', 'A201', 'A201-Farmacia Locatel, C.A.', 'Calle Vargas Edif. Locatel Boleíta Norte', '', 'Caracas', 'VENEZUELA', 'J-30169713-8', '0212-2034433', 'MIRANDA', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x00000000000007D1', '2locatelvzla.com.ve', '', 1, 1, 0, '0008EB92-0DE4-468E-9341-1828D8262EB2', NULL),
(203, 'La Castellana', 'A203', 'A203-FARMACIA SAFEL 777, S.A.', 'Av. San Felipe, Calle el Bosque Urb La Castellana', '', 'Caracas', 'VENEZUELA', 'J-30603501-0', '0212-2745311 1', 'DC', '1030', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D2', 'safelcas.interno', '', 2, 0, 0, '0003A159-ECE4-48FB-B8FC-679225BA563C', NULL),
(213, 'BO Center Cerrada', 'A213', 'A237-FARMACIA CANDEL, C.A.', '', '', 'Caracas', 'VENEZUELA', 'J-30906045-7', '0212-2321111', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D3', '', '', 3, 1, 0, '00006C40-F193-4652-8325-58F997011EAD', NULL),
(215, 'IPSFA Maracay', 'A215', 'A215-INVERSALUD FARMACIA, C.A.', '', '', 'Maracay', 'VENEZUELA', 'J-30675498-9', '0243-2326222', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D4', '', '', 4, 1, 0, '00082E8D-3642-44F2-823E-15ABADFBFDD4', NULL),
(217, 'Santa Paula', 'A217', 'A211-FARMACIA DARANPE, C.A.', 'Av Circunvalación del Sol C.C. El Sol Niv 3 ', '', 'Caracas', 'VENEZUELA', 'J-30681103-6', '0212-9857340', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D5', 'daranpepau.interno', '', 5, 0, 0, '000D271A-EB6C-41D6-8C99-5C93F4BE9777', NULL),
(219, 'Parque Aragua', 'A219', 'A213-INVERFAN FARMACIA, C.A.', '', '', 'Maracay', 'VENEZUELA', 'J-30674714-1', '0243-2328411', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D6', '', '', 6, 1, 0, '0000BCC5-B43A-460B-AAA7-07097584B75A', NULL),
(221, 'Locatel CMDLT', 'A221', 'A217-TRINIGOLD FARMACIA, C.A.', 'Av. Intercomunal El Hatillo Edif. USI II Sótano 3', '', 'Caracas', 'VENEZUELA', 'J-30694383-8', '0212-9445169', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D7', 'TRINIGOLDCMD.INTERNO', '', 7, 0, 0, '000645D9-190C-4D77-AEA7-1FF7A0A11C40', NULL),
(225, 'Plaza Mayor', 'A225', 'A221-FARMACIA CPM, C.A.', '', '', 'Puerto La Cruz', 'VENEZUELA', 'J-30731169-0', '0281-2813763', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D8', '', '', 8, 1, 0, '0007404B-2500-4B75-ACFE-6AF8B80277D7', NULL),
(227, 'La Marrón', 'A227', 'A223-FARMACIA NORAM, C.A.', 'Esq. La Marrón C.C. Doral ', '', 'Caracas', 'VENEZUELA', 'J-30797642-0', '0212-5644322', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007D9', 'NORAMLAM.INTERNO', '', 9, 0, 0, '00034E4D-FA9D-4FFB-B365-400FF8F5EB38', NULL),
(237, 'Puerto Ordaz', 'A237', 'A231-FARMACIA SALUD ARAIMA, C.A.', '', '', 'Puerto Ordaz', 'VENEZUELA', 'J-31645534-3', '0286-9614411', '', '1010', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DA', 'ptoor.locatelvzla.com.ve', '', 10, 1, 0, '000641D9-EF06-4519-8039-1E9296FDD53C', NULL),
(239, 'El Valle', 'A239', 'A233-FARMACIA LOVALLE, C.A.', 'Av. Intercomunal del Valle CC El Valle Niv 8', '', 'Caracas', 'VENEZUELA', 'J-30900959-1', '0212-6716065', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DB', 'LOVALLEVAL.INTERNO', '', 11, 1, 0, '000C1489-F087-4116-88D3-631347ED1900', ''),
(243, 'La Candelaria', 'A243', 'A237-FARMACIA CANDEL, C.A.', 'Av. Sur 13 Candilito a Cruz C.C. Doral Nivel 2', '', 'Caracas', 'VENEZUELA', 'J-30906045-7', '0212-5767911', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DC', 'CANDELCAN.INTERNO', '', 12, 1, 0, '00037826-F446-451D-9ACE-1E52212CCEB9', NULL),
(257, 'Chacaito', 'A257', 'A203-FARMACIA SAFEL 777, S.A.', 'Plaza Brion C.C. Expreso Av. Fco De Miranda', '', 'Caracas', 'VENEZUELA', 'J-30603501-0', '0212-9530191', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DD', 'safelcha.interno', '', 13, 0, 0, '000B7016-ED30-44CA-BE2D-99046B1A92C7', NULL),
(261, 'Caricuao', 'A261', 'A249-FARMACIA 9 ENE, C.A.', 'Av Hacienda UD 3 CC Cuidad Caricuao N 1 Loc LS PB', '', 'Caracas', 'VENEZUELA', 'J-31158393-9', '0212-4327113', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DE', '9ENECAR.INTERNO', '', 14, 1, 0, '00060532-5F9E-4443-9830-70A8E672587D', ''),
(263, 'San Cristobal', 'A263', 'A250-FARMACIA DEL TACHIRA, C.A', '', '', 'San Cristóbal', 'VENEZUELA', 'J-31157664-9', '0276-3414130', '', '1010', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007DF', 'sacri.locatelvzla.com.ve', '', 15, 1, 0, '000190DB-0DCC-47FE-B9F9-AA38E5839D9A', NULL),
(265, 'Propatria', 'A265', 'A253-FARMACIA SALUD GUAYCAR, C.A.', '', '', 'Caracas', 'VENEZUELA', 'J-31160994-6', '0212-8732326', '', '1010', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E0', 'propa.locatelvzla.com.ve', '', 16, 1, 0, '000ACD99-E3F9-4C10-B003-C9F480131395', NULL),
(267, 'Las Delicias Cerrada', 'A267', 'A255-INVERNOR FARMACIA, C.A.', '', '', 'Maracay', 'VENEZUELA', 'J-30981691-8', '0243-2424165', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E1', '', '', 17, 1, 0, '00041B82-46A1-4681-8740-231D00123A9F', NULL),
(269, 'Dos Caminos', 'A269', 'A211-FARMACIA DARANPE, C.A.', 'Av Sucre de Los Dos Caminos 1era y 2da Transv', '', 'Caracas', 'VENEZUELA', 'J-30681103-6', '0212-2850020', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E2', 'daranpedos.interno', '', 18, 0, 0, '000BF049-0A67-4CA6-A8D1-2645F7134136', NULL),
(271, 'La Trinidad', 'A271', 'A217-TRINIGOLD FARMACIA, C.A.', 'Av San Sebastián entre Los Guayabitos', '', 'Caracas', 'VENEZUELA', 'J-30694383-8', '0212-9459934', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E3', 'TRINIGOLDTRI.INTERNO', '', 19, 0, 0, '000D3AB7-D855-42BC-947E-03EB8F56355E', NULL),
(279, 'Punto Fijo', 'A279', 'A261-OMV FARMACIA, C.A', 'Av. Francisco de Miranda C.C. Pacific Center', '', 'Punto Fijo', 'VENEZUELA', 'J-31369832-6', '0269-2478225', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E4', '', '', 20, 1, 0, '000AFEBC-2754-4808-8FF1-960604A8A069', NULL),
(283, 'Acarigua', 'A283', 'A265-FARMEDICA, C.A', '', '', 'Acarigua', 'VENEZUELA', 'J-31363656-8', '0255-6219798', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E5', 'acari.locatelvzla.com.ve', '', 21, 1, 0, '00013AED-8ADD-408E-8FD0-68DFDF99ABF3', NULL),
(291, 'Barinas', 'A291', 'A267-BARINTEL FARMACIA, C.A.', 'Alberto Arvelo C.C. Locatel Jardines Alto Barinas', '', 'Barinas', 'VENEZUELA', 'J-31485089-0', '0273-5417699', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E6', 'BARINTELBAR.INTERNO', '', 22, 0, 0, '000B62A5-8294-4A8F-8108-0CE03E32782D', NULL),
(295, 'Parque Caracas', 'A295', 'A237-FARMACIA CANDEL, C.A.', 'Av. Este C.C. Parque Caracas', '', 'Caracas', 'VENEZUELA', 'J-30906045-7', '0212-5782592', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E7', 'CANDELPAR.INTERNO', '', 23, 1, 0, '000EF232-02C5-48AA-9A17-460E8A00BF2A', NULL),
(296, 'El Paraiso', 'A296', 'A271-FARMACIA LOCPAR 18, C.A.', 'Redoma La India CC Galerías Paraíso Niv Paraíso', '', 'Caracas', 'VENEZUELA', 'J-30749416-6', '0212-4721055', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E8', 'LOCPAR18PAR.INTERNO', '', 24, 0, 0, '0001E37C-EC64-44BC-8904-175546E1B1C9', NULL),
(297, 'Los Teques', 'A297', 'A233-FARMACIA LOVALLE, C.A.', 'Av. La Hoyada con Calle Piar C.C. La Hoyada Piso 3', 'Los Teques', 'Caracas', 'VENEZUELA', 'J-30900959-1', '0212-3217036', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007E9', 'LOVALLETEQ.INTERNO', '', 25, 1, 0, '0003F3AB-4068-4A0A-9FD5-5FF292880A4F', ''),
(300, 'San Antonio', 'A300', 'A273-FARMACIAS DE LOS ALTOS MIRANDINOS (FAMSA)S.A.', 'KM 16 Carretera Panamericana C.C. Club de Campo PB', 'San Antonio', 'Caracas', 'VENEZUELA', 'J-29671532-7', '0212-3736855', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007EA', 'famsaant.interno', '', 26, 1, 0, '00065818-1E09-41D1-BBA6-806187CB017D', ''),
(301, 'Plaza Bolivar', 'A301', 'A223-FARMACIA NORAM, C.A.', 'Av Oeste, Esq. Ppal y Conde Edif. Ambos Mundos', '', 'Caracas', 'VENEZUELA', 'J-30797642-0', '0212-8633519', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007EB', 'NORAMPLA.INTERNO', '', 27, 0, 0, '000DDEB7-9038-4EF6-AA7F-4FCE54A66759', NULL),
(302, 'Las Mercedes', 'A302', 'A274-FARMACIA VERAMED, C.A.', 'Calle Madrid entre Av. Veracruz y Calle Caroní', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-9934453', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007EC', 'VERAMEDMER.INTERNO', '', 28, 0, 0, '000BA12E-89FB-474A-BE4F-B0248D9FB3E1', NULL),
(303, 'Valle Arriba', 'A303', 'A274-FARMACIA VERAMED, C.A.', 'C.C. Valle Arriba Market Center', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-9756601', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007ED', 'VERAMEDVAL.INTERNO', '', 29, 1, 0, '00076C80-FA2C-4DB1-AE75-67EBE6E2B21A', ''),
(304, 'Santa Monica', 'A304', 'A274-FARMACIA VERAMED, C.A.', 'Calle Arturo Michelena cruce con Calle Lazo Martí', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-6616263', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007EE', 'VERAMEDMON.INTERNO', '', 30, 0, 0, '00002F1D-064E-474C-BA22-C90653449BB3', NULL),
(305, 'El Tigre', 'A305', 'A275-FARMACIA 4B, C.A.', 'Av. Francisco de Miranda Nº240 C.C. Petrucci', '', 'El Tigre', 'VENEZUELA', 'J-29691375-7', '0283-2317303', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007EF', '4BTIG.INTERNO', '', 31, 1, 0, '0003F882-0292-4163-A4E2-5EB46C44F136', NULL),
(306, 'Cabimas', 'A306', 'A276-FARMACIA COLSALUD, C.A.', 'Av. Intercomunal Esq. Vereda Tropical Cinco Bocas', '', 'Cabimas', 'VENEZUELA', 'J-29843095-8', '0264-8155353', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F0', 'cabim.locatelvzla.com.ve', '', 32, 1, 0, '0001AF85-3BBB-4D70-A2B0-F884C80585AA', NULL),
(309, 'El Viñedo', 'A309', 'A277-FARMACIA SOTAVENTO, C.A.', 'Av. Carlos Sanda Nro. 104146 Urb. El Viñedo', '', 'Valencia', 'VENEZUELA', 'J-29929929-4', '0241-8210455', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F1', 'SOTAVENTOVIÑ.INTERNO', '', 33, 0, 0, '0005FA57-2D98-46C3-A816-B549007DA04D', NULL),
(311, 'Guaparo', 'A311', 'A277-FARMACIA SOTAVENTO, C.A.', 'Av. Ppal de Guaparo C.C. Guaparo Planta Alta', '', 'Valencia', 'VENEZUELA', 'J-29929929-4', '0241-8255455', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F2', 'SOTAVENTOGUA.INTERNO', '', 34, 0, 0, '000D09CD-2B2D-4E15-99BA-9FA9B0A26651', NULL),
(312, 'Buenaventura', 'A312', 'A278-FARMACIA LAS LLAVES, C.A.', 'Av. Intercomunal Guarenas Guatire C.C. Buenaventura', '', 'Guatire', 'VENEZUELA', 'J-29940149-8', '0212-3810165', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F3', 'LASLLAVESBUE.INTERNO', '', 35, 1, 0, '0000D323-5A06-4A73-A3B9-216A6C77413B', ''),
(313, 'Alto Prado', 'A313', 'A274-FARMACIA VERAMED, C.A.', 'Av Ppal Alto Prado CC Alto Prado Nivel PB Local 43', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-9773235', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F4', 'VERAMEDALT.INTERNO', '', 36, 0, 0, '00030E39-C434-453A-A64B-6B8F86E80CC1', NULL),
(314, 'Coro', 'A314', 'A279-FARMACIA LOS MEDANOS, C.A.', 'Av. Independencia cruce con Av. Los Médanos', '', 'Coro', 'VENEZUELA', 'J-29928379-7', '0268-2521122', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F5', 'coro.locatelvzla.com.ve', '', 37, 1, 0, '0003D1A5-B77F-4D29-A785-35EA63989350', NULL),
(315, 'Maturín', 'A315', 'A280-FARMACIA MONAGAS, C.A.', 'Av. Alirio Ugarte Pelayo C.C. Monagas Plaza PB', '', 'Maturín', 'VENEZUELA', 'J-29938833-5', '0291-6435311', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F6', 'MONAGASMAT.INTERNO', '', 38, 1, 0, '0003D4EE-B0AA-461B-871B-947385EC536A', NULL),
(317, 'Lechería', 'A317', 'A281-FARMACIA EL MORRO, C.A.', 'Av. Ppal de Lechería C.C. Anna Planta Baja', '', 'Lechería', 'VENEZUELA', 'J-29934151-7', '0281-2865561', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F7', 'ELMORROLEC.INTERNO', '', 39, 1, 0, '00062C95-B355-449D-807B-8BA08E084D4C', NULL),
(319, 'La Victoria', 'A319', 'A282-FARMACIA LAS MERCEDES DE LA VICTORIA, C.A.', 'Av. Intercomunal La Victoria C.C. Morichal PB', '', 'La Victoria', 'VENEZUELA', 'J-29938596-4', '0244-3221144', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F8', 'LASMERCEDESVIC.INTERNO', '', 40, 1, 0, '0001097F-BF90-4965-9F7C-99B6A0E8D796', NULL),
(321, 'Valle de La Pascua', 'A321', 'A283-FARMACIA LA PASCUA, C.A.', 'Av. Rómulo Gallegos cruce con Calle Atarraya', '', 'Valle De La Pascua', 'VENEZUELA', 'J-29938600-6', '0235-3413344', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007F9', 'LAPASCUAVAL.INTERNO', '', 41, 1, 0, '000E0E7E-709E-4552-BB86-7E19602B0FDB', NULL),
(322, 'Guatire', 'A322', 'A274-FARMACIA VERAMED, C.A.', 'C.C. Castillejo Urb. El Marques ', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-3410864', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007FA', 'VERAMEDGUA.INTERNO', '', 42, 0, 0, '00000E6E-B962-4D39-86B3-38B6EBA4CEB8', NULL),
(323, 'Cumaná', 'A323', 'A284-FARMACIA SUCRE 24, C.A.', 'Av. Perimetral C.C. Cumaná Plaza PB Locales 1 y 2', '', 'Cumaná', 'VENEZUELA', 'J-29940428-4', '0293-4519961', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007FB', 'SUCRE24CUM.INTERNO', '', 43, 1, 0, '0007889A-082D-4E16-953B-3B1B76C1860E', NULL),
(325, 'Valencia Centro', 'A325', 'A277-FARMACIA SOTAVENTO, C.A.', 'Av. Bolívar Norte C.C. Locatel Planta Baja', '', 'Valencia', 'VENEZUELA', 'J-29929929-4', '0241-8588555', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x0000000000000813', 'SOTAVENTOCEN.INTERNO', '', 44, 1, 0, '0005C66C-C0EB-4D88-8422-C89C1F8C5815', NULL),
(327, 'Valencia Sur', 'A327', 'A277-FARMACIA SOTAVENTO, C.A.', 'Av. Las Ferias Urb. Santa Rosa C.C. Locatel', '', 'Valencia', 'VENEZUELA', 'J-29929929-4', '0241-8318255', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000814', 'SOTAVENTOSUR.INTERNO', '', 45, 1, 0, '000C19A0-0A1D-4C5C-9A5C-F871E9283742', NULL),
(329, 'Petare', 'A329', 'A287-FARMACIA SALUPET, C.A.', 'Av. Fco de Miranda Redoma de Petare C.C. Las Vegas', '', 'Caracas', 'VENEZUELA', 'J-40200905-4', '0212-2566355', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x0000000000000815', 'SALUPETPET.INTERNO', '', 46, 0, 0, '00049DB3-7476-4F1E-9C78-5085C5E64B62', NULL),
(331, 'Ciudad Bolívar', 'A331', 'A288-FARMACIA ANGOSTURA, C.A.', 'Av. Germania C.C. Locatel PB', '', 'Ciudad Bolívar', 'VENEZUELA', 'J-29952210-4', '0285-6323322', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007FC', 'ANGOSTURACIU.INTERNO', '', 50, 1, 0, '0000100C-0AF2-4FD0-A59B-501A7B4CE26F', NULL),
(333, 'Guarenas', 'A333', 'A274-FARMACIA VERAMED, C.A.', 'Av. Intercomunal Guarenas Urb. El Saman Lote A2', '', 'Caracas', 'VENEZUELA', 'J-30926957-7', '0212-3699090', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '0x00000000000007FD', 'VERAMEDGUE.INTERNO', '', 52, 0, 0, '00020B04-370A-464A-A80B-035985C778F3', NULL),
(334, 'Cagua', 'A334', 'A293-FARMACIA LCT 18, C.A.', 'Carretera Nacional Cagua-La Villa C.C. Cagua PB', '', 'Cagua', 'VENEZUELA', 'J-50202212-0', '0244-3965566', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000803', 'LCT18CAG.INTERNO', '', 61, 1, 0, '675A0ED3-4853-4886-B9B3-A00D746D47AA', NULL),
(337, 'El Marques', 'A337', 'A293-FARMACIA LCT 18, C.A.', 'Av Fco de Miranda C.C. Unicentro El Marques PB', '', 'Caracas', 'VENEZUELA', 'J-50202212-0', '0212-2391616', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x00000000000007FE', 'LCT18MAR.INTERNO', '', 56, 0, 0, '6D40F117-6407-422C-B25A-93F940F50A8F', NULL),
(338, 'Fenix San Bernardino', 'A338', 'A293-FARMACIA LCT 18, C.A.', 'Av Villegas entre Av Panteón y Av Los Próceres ', '', 'Caracas', 'VENEZUELA', 'J-50202212-0', '0424-2577758', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x00000000000007FF', 'LCT18BER.INTERNO', '', 57, 0, 0, '4A909063-22B2-4328-8B42-6B492B455325', NULL),
(339, 'Sigo Maracay', 'A339', 'A293-FARMACIA LCT 18, C.A.', 'Av. Intercomunal Maracay C.C. Parque Los Aviadores', '', 'Maracay', 'VENEZUELA', 'J-50202212-0', '0243-2011111', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000804', 'LCT18SIG.INTERNO', '', 62, 1, 0, 'D1871C0F-5079-4B52-87C5-C30D42037C39', NULL),
(341, 'Las Delicias', 'A341', 'A293-FARMACIA LCT 18, C.A.', 'Av. Las Delicias CC Las Américas PB Maracay', '', 'Maracay', 'VENEZUELA', 'J-50202212-0', '0243-2424165', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000801', 'LCT18DEL.INTERNO', '', 59, 0, 0, '7AF56E74-F3F3-43C6-BA5B-32F51B27341D', NULL),
(342, 'San Juan de los Morros', 'A342', 'A293-FARMACIA LCT 18, C.A.', 'Sector la Morera Avenida Sendrea Quinta Alida ', '', 'San Juan de los Morros', 'VENEZUELA', 'J-50202212-0', '0246-4311025', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000802', 'LCT18MOR.INTERNO', '', 60, 1, 0, '0B9A845C-D8A8-43C4-82FD-E4EA44933923', NULL),
(343, 'Aviadores', 'A343', 'A293-FARMACIA LCT 18, C.A.', 'C.C. Parque Los Aviadores Local L-160 Palo Negro', '', 'Maracay', 'VENEZUELA', 'J-50202212-0', '0243-2019848', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000800', 'LCT18AVI.INTERNO', '', 58, 0, 0, '9738AF29-14DB-4265-A5D8-C013C9E423E1', NULL),
(344, 'Boleita Farmacia', 'A344', 'A293-FARMACIA LCT 18, C.A.', 'Calle Vargas Edif. Pioneer Nro 17 - Boleita Norte', '', 'Caracas', 'VENEZUELA', 'J-50202212-0', '0212-2034433', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x0000000000000811', 'LCT18BOL.INTERNO', '', 66, 0, 0, '000C61ED-19AA-4ED3-9D12-B4505CF55623', ''),
(345, 'Clínicas Caracas', 'A345', 'A293-FARMACIA LCT 18, C.A.', 'Av Panteón con Av Alameda CC Clínicas Caracas PB', '', 'Caracas', 'VENEZUELA', 'J-50202212-0', '0212-2034505', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2022-08-01 20:27:02.243', '0x0000000000000805', 'LCT18CLI.INTERNO', '', 63, 0, 0, 'E1500055-661C-46DA-8149-65A0E177CD8F', NULL),
(346, 'Caricuao Farmacia', 'A346', 'A297-FARMACIA TOP 24, C.A', 'UD7 Caricuao Urb. Hacienda Casarapa C.C. Caricuao', '', 'Caracas', 'VENEZUELA', 'J-50617248-8', '0212-2745311', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x0000000000000810', 'TOP24CAR.INTERNO', '', 69, 0, 0, '672E9D47-73BA-4E4C-9B3B-866418386370', ''),
(347, 'San Martín Farmacia', 'A347', 'A297-FARMACIA TOP 24, C.A', 'Slimak Edif. Jayor Piso P.B. Local Unico Calle Ber', '', 'Caracas', 'Venezuela', 'J-50617248-8', '0212-2745311', 'Distrito Capital', '1020', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x0000000000000812', 'TOP24MAR.INTERNO', '', 67, 0, 0, '57B77A38-9993-4B97-8AF0-D81B4B6F7A85', ''),
(348, 'El Valle Farmacia', 'A348', 'A297-FARMACIA TOP 24, C.A', 'Calle 14 con Calle Intercomunal C.C. El Valle Niv 8', '', 'Caracas', 'VENEZUELA', 'J-50617248-8', '0212-2745311', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x000000000000080E', 'TOP24VAL.INTERNO', '', 65, 0, 0, '4A90A2DA-18F6-4B17-A0FA-7A7165842813', ''),
(349, 'San Antonio Farmacia', 'A349', 'A297-FARMACIA TOP 24, C.A', 'Carretera Panamericana Km 16 C.C Club de Campo PB', 'San Antonio', 'Caracas', 'VENEZUELA', 'J-50617248-8', '0212-2745311', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x000000000000080D', 'TOP24ANT.INTERNO', '', 64, 0, 0, '4E8A6313-2E85-4ED0-811C-8451CEB72740', ''),
(350, 'Locatel Los Teques', 'A350', 'A297-FARMACIA TOP 24, C.A', 'Calle La Hoyada con Calle Piar C.C. La Hoyada Piso 3', 'Los Teques', 'Los Teques', 'VENEZUELA', 'J-50617248-8', '0212-2745311', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-11-05 13:52:32.490', '0x000000000000080F', 'TOP24TEQ.INTERNO', '', 68, 0, 0, '0EE61022-7788-4444-9343-690373ED060F', '');

SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[Supplier](
[Country] [nvarchar](20) NOT NULL,
[HQID] [int] NOT NULL,
[LastUpdated] [datetime] NOT NULL,
[State] [nvarchar](20) NOT NULL,
[ID] [int] IDENTITY(1,1) NOT NULL,
[SupplierName] [nvarchar](30) NOT NULL,
[ContactName] [nvarchar](30) NOT NULL,
[Address1] [nvarchar](30) NOT NULL,
[Address2] [nvarchar](30) NOT NULL,
[City] [nvarchar](30) NOT NULL,
[Zip] [nvarchar](20) NOT NULL,
[EmailAddress] [nvarchar](255) NOT NULL,
[WebPageAddress] [nvarchar](255) NOT NULL,
[Code] [nvarchar](17) NOT NULL,
[DBTimeStamp] [timestamp] NULL,
[AccountNumber] [nvarchar](20) NOT NULL,
[TaxNumber] [nvarchar](20) NOT NULL,
[CurrencyID] [int] NOT NULL,
[PhoneNumber] [nvarchar](30) NOT NULL,
[FaxNumber] [nvarchar](30) NOT NULL,
[CustomText1] [nvarchar](30) NOT NULL,
[CustomText2] [nvarchar](30) NOT NULL,
[CustomText3] [nvarchar](30) NOT NULL,
[CustomText4] [nvarchar](30) NOT NULL,
[CustomText5] [nvarchar](30) NOT NULL,
[CustomNumber1] [float] NOT NULL,
[CustomNumber2] [float] NOT NULL,
[CustomNumber3] [float] NOT NULL,
[CustomNumber4] [float] NOT NULL,
[CustomNumber5] [float] NOT NULL,
[CustomDate1] [datetime] NULL,
[CustomDate2] [datetime] NULL,
[CustomDate3] [datetime] NULL,
[CustomDate4] [datetime] NULL,
[CustomDate5] [datetime] NULL,
[Notes] [ntext] NOT NULL,
[Terms] [nvarchar](50) NOT NULL,
[SyncGuid] [uniqueidentifier] NULL,
 CONSTRAINT [PK_Supplier] PRIMARY KEY NONCLUSTERED
(
[ID] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Country]  DEFAULT ('') FOR [Country]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_HQID]  DEFAULT ((0)) FOR [HQID]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_LastUpdated]  DEFAULT (getdate()) FOR [LastUpdated]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_State]  DEFAULT ('') FOR [State]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_SupplierName]  DEFAULT ('') FOR [SupplierName]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_ContactName]  DEFAULT ('') FOR [ContactName]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Address1]  DEFAULT ('') FOR [Address1]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Address2]  DEFAULT ('') FOR [Address2]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_City]  DEFAULT ('') FOR [City]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Zip]  DEFAULT ('') FOR [Zip]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_EmailAddress]  DEFAULT ('') FOR [EmailAddress]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_WebPageAddress]  DEFAULT ('') FOR [WebPageAddress]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Code]  DEFAULT ('') FOR [Code]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_AccountNumber]  DEFAULT ('') FOR [AccountNumber]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_TaxNumber]  DEFAULT ('') FOR [TaxNumber]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_CurrencyID]  DEFAULT ((0)) FOR [CurrencyID]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_PhoneNumber]  DEFAULT ('') FOR [PhoneNumber]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_FaxNumber]  DEFAULT ('') FOR [FaxNumber]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomText1]  DEFAULT ('') FOR [CustomText1]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomText2]  DEFAULT ('') FOR [CustomText2]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomText3]  DEFAULT ('') FOR [CustomText3]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomText4]  DEFAULT ('') FOR [CustomText4]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomText5]  DEFAULT ('') FOR [CustomText5]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomNumber1]  DEFAULT ((0)) FOR [CustomNumber1]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomNumber2]  DEFAULT ((0)) FOR [CustomNumber2]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomNumber3]  DEFAULT ((0)) FOR [CustomNumber3]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomNumber4]  DEFAULT ((0)) FOR [CustomNumber4]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_CustomNumber5]  DEFAULT ((0)) FOR [CustomNumber5]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [Df_Supplier_Notes]  DEFAULT ('') FOR [Notes]
GO

ALTER TABLE [dbo].[Supplier] ADD  CONSTRAINT [DF_Supplier_Terms]  DEFAULT ('') FOR [Terms]
GO
SET IDENTITY_INSERT dbo.Supplier ON;

INSERT INTO dbo.Supplier (
    Country, HQID, LastUpdated, State, ID, SupplierName, ContactName,
    Address1, Address2, City, Zip, EmailAddress, WebPageAddress,
    Code, AccountNumber, TaxNumber, CurrencyID,
    PhoneNumber, FaxNumber, CustomText1, CustomText2, CustomText3,
    CustomText4, CustomText5, CustomNumber1, CustomNumber2,
    CustomNumber3, CustomNumber4, CustomNumber5, CustomDate1,
    CustomDate2, CustomDate3, CustomDate4, CustomDate5, Notes, Terms, SyncGuid
) VALUES
('VE', 0, '2024-04-08 14:10:54', '', 43, 'ASPEN REPRESENTACIONES, C.A.', 'NEYRA RUIZ', 'BOLEITA NORTE', 'CLL. VARGAS, EDIF. PIONEER N/1', 'CARACAS', '1070', '', '', '100175', 'J-30454258-5', '', 0, '582122034433', '2034519', '', '', '', '', '', 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, '', '', '0005E22B-C1A5-452A-BAFC-1154A0466D03'),
('VE', 0, '2023-10-16 15:33:28', '', 44, 'ALFONZO RIVAS CIA, C.A.', 'STORY', 'EDIF.GENERAL DE SEGUROS, PS. 8', 'AV. LA ESTANCIA,', 'CHUAO, CARACAS', '1060', 'JRAMIREZ@ALFONZORIVAS.COM', '', '100182', 'J-00031531-0', '', 0, '582127009103/...', '7009082', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:28', NULL, NULL, NULL, NULL, '', '', '000FE4B9-F9BE-4BE3-824D-B920F4A416C8'),
('VE', 0, '2023-10-16 15:33:29', '', 45, 'ALIMENTOS HEINZ, C.A.', 'DELGADO', 'CARRET. SAN JOAQUINSAN JOAQUIN', '(ANTES IND. ALIM. DEL CENTRO C', 'SAN JOAQUIN, CARABOBO', '2018', 'antonio.orellano@ve.hjheinz.com', '', '100184', 'J-07586215-5', '', 0, '582122576380/...', '2575533', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '0001EE62-621D-490F-BD7F-69552E38F2B1'),
('VE', 0, '2023-10-16 15:33:29', '', 46, 'ALIMENTOS LA INTEGRAL, C.A.', 'LEANDRO', 'PALMA SOLA', 'VIA SALON, SECTOR PALMA SOLA,', 'NIRGUA, YARACUY', '3205', 'williamhernandez0509@gmail.com', '', '100186', 'J-08530623-4', '', 0, '582545751011', '5751020', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '00035706-C5D9-4879-AC30-B9DB8133A47F'),
('VE', 0, '2023-10-16 15:33:29', '', 47, 'ALIMENTOS POLAR COMERCIAL, C.A', 'MONTILLA', 'EDIF. CENTRO EMPRESARIAL POLAR', '4TA TRANSVERSAL CON 2DA. AVENI', 'LOS CORTIJOS, CARACAS', '1071', 'yaniree.caceres@empresas-polar.com', '', '100187', 'J-00041312-6', '', 0, '582122027415/...', '2027051', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '000F4954-5A4C-4F03-8D9E-052B517336E1'),
('VE', 0, '2023-10-16 15:33:29', '', 48, 'ALIMENTOS POS 3, C.A.', 'GONZALEZ', 'ED. LA LOMBARDA, P02LA TRINIDA', 'Z.IND. LA TRINIDAD C.LA SOLEDA', 'LA TRINIDAD, CARACAS', '1010', 'CHOCOCHITAS.VE@GMAIL.COM', '', '100188', 'J-00311276-3', '', 0, '582129416820/...', '9443521', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '000BAA86-83F1-4B46-89C5-C0EDEDD7A1C5'),
('VE', 0, '2023-10-16 15:33:29', '', 49, 'ALPINA PRODUCTOS ALIMENTICIOS,', 'LAURA MOLINA', 'BERNADETTE EDIF.ROCHE,ALA SUR ', 'AV. DIEGO CISNERO C/CALLE', 'LOS RUICES, CARACAS', '1060', 'lmolina@alpina.com.ve', '', '100189', 'J-30095020-4', '', 0, '0212-2560707', '0212-2563346', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '0007C726-B272-46B3-8C8D-281D6FB5E5B7'),
('VE', 0, '2023-10-16 15:33:29', '', 50, 'ARCO IRIS LABORATORIO, C.A.', 'SUMOZA', 'NIVEL PB LOCAL NRO 19, URB LA ', 'CALLE BOLIVAR CC LA CANDELARIA', 'EL LIMON, ARAGUA', '2105', 'marinarubio2108@gmail.com', '', '100191', 'J-30674474-6', '', 0, '582432835934/...', '2833747', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '000AC716-74A7-44F5-8F23-FB1A258B2DD2'),
('VE', 0, '2023-10-16 15:33:29', '', 51, 'BERSA-CHEM ANDINA, S.A.', 'AVILA', 'EDF. DILCAN, PISO 1 LOC. 1/3LA', 'AV. URDANETA ESQ.CANDALITO A U', 'LA CANDELARIA, CARACAS', '1010', 'bersachem@gmail.com', '', '100196', 'J-30512574-0', '', 0, '582122321098', '2321098', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '0008720C-D13F-40A1-8780-21FC657782B0'),
('VE', 0, '2023-10-16 15:33:29', '', 52, 'GENIA CARE, C.A.', 'JENNIFER VOLCAN/MARIANA ACUNA', 'ESQ. CON AVENIDA EL PARQUEEDIF', 'AV FRANCISCO DE MIRANDA', 'EL BOSQUE, CARACAS', '1060', 'beny.torres@geniacare.com', '', '100197', 'J-00068171-6', '', 0, '0212-7007800', '0212-2372617', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:29', NULL, NULL, NULL, NULL, '', '', '0000EAB3-58DE-44E3-98CF-02E7D2D74004'),
('VE', 0, '2023-10-16 15:33:30', '', 53, 'C.A., VITA', 'PEREZ', 'C.C. EL RECREO, TORRE NORTE, P', 'AV. CASANOVA CON CALLE EL RECR', 'SABANA GRANDE, CARACAS', '1050', 'AALFONZO@LABVITA.COM', '', '100198', 'J-00038704-4', '', 0, '582127933793', '582127815264', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000D14A4-140A-4373-B2E8-44F46B0B2CA5'),
('VE', 0, '2023-10-16 15:33:30', '', 54, 'C.A, SUCESORA DE JOSE PUIG CIA', 'RONDON', 'LOS CORTIJOS DE LOURDES', 'FINAL 2DA. TRANV. ED. PUIG', 'LOS CORTIJOS, CARACAS', '1071', 'ventnacionales@galletaspuig.com', '', '100199', 'J-00030361-4', '', 0, '582122391846', '2355291', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000C7A66-02B0-44E4-934E-3A5333FBDDEA'),
('VE', 0, '2023-10-16 15:33:30', '', 55, 'CERVECERIA POLAR, C.A.', 'CARLOS AUGUSTO LEON', 'PLANTA POLARLOS CORTIJOS DE LO', '2DA. AV.DE LOS CORTIJOS DE LOU', 'LOS CORTIJOS, CARACAS', '1071', 'cs.cuentasporcobrarco@empresas-polar.com', '', '100205', 'J-00006372-9', '', 0, '582122023800', '2023800', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000AD461-2140-4BEB-9BCF-390378EC0A2D'),
('VE', 0, '2023-10-16 15:33:30', '', 56, 'CHOCOLATES KRON, C.A.', 'LIDIA DIAZ', 'CALLE LA TINAJA, PB LOC. BEL L', 'AV. TAMANACO, ED. FABRICIO,', 'EL LLANITO, CARACAS', '1062', '', '', '100206', 'J-00349535-2', '', 0, '582122710935', '2710935', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000F7EC4-64D3-4860-9ABA-D5D1486BA41F'),
('VE', 0, '2023-10-16 15:33:30', '', 57, 'CHOCOLATES ST.MORITZ, C.A.', 'JIMENEZ', 'PALO VERDE', 'Z. INDT. EDIF. ALEXANDRA LOCAL', 'PALO VERDE, CARACAS', '1070', 'CSUAREZ@CHOCOLATESMORITZ.COM', '', '100207', 'J-30153474-3', '', 0, '582122516834', '2513995', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000B7792-15A6-4241-856C-0BEEB9752239'),
('VE', 0, '2023-10-16 15:33:30', '', 58, 'COLGATE PALMOLIVE, C.A.', 'HENRY VALERO', 'PISO 1 Y 2LOS CORTIJOS DE LOUR', 'CALLE HANS NEUMANN, EDF. CORIM', 'LOS CORTIJOS, CARACAS', '1010', 'henry_valero@colpal.com', '', '100210', 'J-00007125-0', '', 0, '582122074804', '2375651', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000F039B-0279-4AC3-A95C-7AC39FBFD756'),
('VE', 0, '2023-10-16 15:33:30', '', 59, 'COMERCIALIZADORA LUDI, C.A.', 'RICHARD DIAZ', 'PS. 3, AP. 3DLAS ACACIAS', 'AV. GRAN COLOMBIA, RESD. AMALI', 'LAS ACACIAS, CARACAS', '1040', 'comludi@cantv.net', '', '100212', 'J-30994932-2', '', 0, '582126311724', '6311724', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000304E7-535B-418B-AF73-106532DF3625'),
('VE', 0, '2023-10-16 15:33:30', '', 60, 'CORPORACION EMCETA, C.A.', 'MARQUEZ', 'CASA ROSA MARIA URB BOLEITA SU', 'AV PRINCIPAL, 3ERA TRANSVERSAL', 'BOLEITA, CARACAS', '1070', 'creditoycobranza@naturalsystem.com', '', '100218', 'J-30451866-8', '', 0, '582122371868', '2396977', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '00081656-4CAD-44A5-9DA3-33A10CA4E705'),
('VE', 0, '2023-10-16 15:33:30', '', 61, 'CORPORACION GIPSY', 'KIENZLER', 'PARCELA 45 URB. ZONA INDUSTRIA', 'AV. PPAL GUAYABAL', 'GUARENAS, MIRANDA', '1070', 'LHUNG@GRUPOGIPSY.COM', '', '100219', 'J-30441114-6', '', 0, '582122388022', '2388022', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '00036361-5740-46B7-A6B0-61BF2ABA7E6B'),
('VE', 0, '2023-10-16 15:33:30', '', 62, 'CORPORACION JAK FARM, C.A.', 'ESCOBAR', 'MEZZANINA OFIC 1 URB CHACAO CA', 'AV.FCO.DE MIRANDA EDF. CARO,', 'CHACAO, CARACAS', '1060', 'CORPORACIONJAKFARMCA@GMAIL.COM', '', '100222', 'J-30260382-0', '', 0, '582122642654', '2657709', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:30', NULL, NULL, NULL, NULL, '', '', '000D2FAA-5CE3-4C31-B72A-90AFC215712D'),
('VE', 0, '2023-10-16 15:33:31', '', 63, 'CORPORACION MIDUCHY, C.A.', 'TOVAR', 'SECTOR CJTO INDUSTRIAL SAN BER', 'CALLE LAS INDUSTRIAS LOCAL NRO', 'LOS TEQUES, MIRANDA', '1203', 'MIDUCHY@GMAIL.COM', '', '100225', 'J-31067984-3', '', 0, '0212-3833325', '3833325', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:31', NULL, NULL, NULL, NULL, '', '', '000F2F19-40A6-4F6F-B5C1-3E2F9E4839AC'),
('VE', 0, '2023-10-16 15:33:31', '', 64, 'COSMETICOS ROLDA, C.A.', 'BENEDETTO', 'LOMAS DE URQUIA, LOCAL 5, PBCA', 'CALLE INDUSTRIAS, 1 ERA TRANSV', 'LOS TEQUES, MIRANDA', '1203', 'recepcion@rolda.com.ve', '', '100230', 'J-00367287-4', '', 0, '582123832766', '3833766', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:31', NULL, NULL, NULL, NULL, '', '', '0005F457-5455-4105-9F65-7AA5058E0D0D'),
('VE', 0, '2023-10-16 15:33:31', '', 65, 'D.H.LIBROS, C.A.', 'IGNACIO DUARTE', 'CHAPELLIN LA FLORIDA QTA. MIKR', 'AV. LOS MANGOS CON ESQ. EL BOS', 'CARACAS', '1050', '', '', '100232', 'J-30622616-8', '', 0, '0212-7303887', '0212-7306473', '', '', '', '', '', 0, 0, 0, 0, 0, '2023-10-16 15:33:31', NULL, NULL, NULL, NULL, '', '', '000EC15C-7DBF-441A-829C-5D87F3554AC3');

SET IDENTITY_INSERT dbo.Supplier OFF;
