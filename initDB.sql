CREATE TABLE Statistics
(
    Id            INT AUTO_INCREMENT NOT NULL,
    HomeViewCount INT                NOT NULL,
    PRIMARY KEY (Id)
);
CREATE TABLE Admin
(
    Id       INT AUTO_INCREMENT NOT NULL,
    Username VARCHAR(64)        NOT NULL,
    Password VARCHAR(64)        NOT NULL,
    Token    VARCHAR(64),
    PRIMARY KEY (Id)
);
CREATE TABLE Category
(
    Id          INT AUTO_INCREMENT NOT NULL,
    ParentId    INT,
    Code        VARCHAR(32)        NOT NULL UNIQUE,
    Title       VARCHAR(64)        NOT NULL,
    Description VARCHAR(512),
    PRIMARY KEY (Id),
    CONSTRAINT Parent_Category_Constraint
        FOREIGN KEY (ParentId)
            REFERENCES Category (Id)
            ON DELETE CASCADE
);
CREATE TABLE Product
(
    Id               INT AUTO_INCREMENT NOT NULL,
    CategoryId       INT                NOT NULL,
    SKU              VARCHAR(64)        NOT NULL UNIQUE,
    Title            VARCHAR(64)        NOT NULL,
    Brand            VARCHAR(64)        NOT NULL,
    Price            INT                NOT NULL,
    ShortDescription VARCHAR(128)       NOT NULL,
    Description      VARCHAR(512)       NOT NULL,
    Image            VARCHAR(256)       NOT NULL,
    Enabled          BOOL               NOT NULL,
    Featured         BOOL               NOT NULL,
    ViewCount        INT                NOT NULL,
    PRIMARY KEY (Id),
    FOREIGN KEY (CategoryId) REFERENCES Category (Id)
);
INSERT INTO Statistics(HomeViewCount)
VALUES (0);
INSERT INTO Admin(Username, Password, Token)
VALUES ('andrija','$2y$10$unL8TXcXCtzR4bKFM4XezeTNSoTkyGoG1SUOPQzJUtm6YrwkFTnIC','123');