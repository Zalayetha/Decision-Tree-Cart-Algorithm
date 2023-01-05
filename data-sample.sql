CREATE TABLE IF NOT EXISTS data_sample (
    `Buying` VARCHAR(3) CHARACTER SET utf8,
    `Maintenance` VARCHAR(3) CHARACTER SET utf8,
    `Doors` VARCHAR(4) CHARACTER SET utf8,
    `Person` VARCHAR(4) CHARACTER SET utf8,
    `Lugage_boot` VARCHAR(5) CHARACTER SET utf8,
    `Safety` VARCHAR(4) CHARACTER SET utf8,
    `Evaluation` VARCHAR(5) CHARACTER SET utf8
);
INSERT INTO data_sample VALUES
    ('Low','med','3','More','big','med','good'),
    ('Low','med','3','More','big','high','vgood'),
    ('Low','med','4','2','small','low','unacc'),
    ('Low','med','4','2','small','med','unacc'),
    ('Low','med','4','More','big','high','vgood'),
    ('Low','med','more','2','small','low','unacc'),
    ('Low','med','more','4','small','med','acc'),
    ('Low','med','more','4','small','high','good'),
    ('Low','med','more','4','med','low','unacc'),
    ('Low','med','more','4','med','med','good'),
    ('Low','med','more','4','med','high','vgood'),
    ('Low','med','more','4','big','low','unacc'),
    ('Low','med','more','4','big','med','good'),
    ('Low','med','more','4','big','high','vgood'),
    ('Low','med','more','More','small','low','unacc'),
    ('Low','med','more','More','small','med','acc');
