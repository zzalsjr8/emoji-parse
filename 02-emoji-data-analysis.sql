/*
------------------------------------------------------------------------
REVIEW DATA
------------------------------------------------------------------------
*/

-- table structures

SHOW CREATE TABLE `emojis`;
SHOW CREATE TABLE `categories`;

--  categories

select `group`, count(*) from emojis group by `group`;

/*
group            |count(*)|
-----------------+--------+
Smileys & Emotion|     171|
People & Body    |     388|
Animals & Nature |     160|
Food & Drink     |     131|
Travel & Places  |     219|
Activities       |      85|
Objects          |     266|
Symbols          |     224|
Flags            |     270|
*/

/*
id|sequence|category         |tag                |emoji|
--+--------+-----------------+-------------------+-----+
 1|       1|Smileys & Emotion|smileys-and-emotion|😀   |
 2|       2|People & Body    |people-and-body    |🧒   |
 3|       3|Animals & Nature |animals-and-nature |🐻   |
 4|       4|Food & Drink     |food-and-drink     |🍔   |
 5|       6|Travel & Places  |travel-and-places  |🚀   |
 6|       5|Activities       |activities         |⚽   |
 7|       7|Objects          |objects            |💡   |
 8|       8|Symbols          |symbols            |💕   |
 9|       9|Flags            |flags              |🎌   |
*/

select c.category
, c.tag
, c.`sequence` 
, c.emoji 
, count(*) ct
from emojis e 
join categories c on e.category_id = c.id 
group by c.category
, c.tag
, c.`sequence` 
, c.emoji 
order by c.`sequence`;

/*
category         |tag                |sequence|emoji|ct |
-----------------+-------------------+--------+-----+---+
Smileys & Emotion|smileys-and-emotion|       1|😀   |171|
People & Body    |people-and-body    |       2|🧒   |388|
Animals & Nature |animals-and-nature |       3|🐻   |160|
Food & Drink     |food-and-drink     |       4|🍔   |131|
Activities       |activities         |       5|⚽   | 85|
Travel & Places  |travel-and-places  |       6|🚀   |219|
Objects          |objects            |       7|💡   |266|
Symbols          |symbols            |       8|💕   |224|
Flags            |flags              |       9|🎌   |270|
*/

-- subgroup count

select e.subgroup 
, count(*) ct
from emojis e 
join categories c on e.category_id = c.id 
where c.id = 2
group by e.subgroup;

/*
subgroup            |ct|
--------------------+--+
hand-fingers-open   |11|
hand-fingers-partial| 9|
hand-single-finger  | 7|
hand-fingers-closed | 6|
hands               | 7|
hand-prop           | 3|
body-parts          |18|
person              |28|
person-gesture      |30|
person-role         |82|
person-fantasy      |33|
person-activity     |58|
person-sport        |43|
person-resting      | 5|
family              |37|
person-symbol       |11|
*/

-- version count

select lpad(replace(e.version, "E", ""),4, 0) version 
, count(*) ct
from emojis e 
join categories c on e.category_id = c.id 
-- where c.id = 2
group by lpad(replace(e.version, "E", ""),4, 0)
order by lpad(replace(e.version, "E", ""),4, 0);

/*
version|ct |
-------+---+
00.6   |719|
00.7   |139|
01.0   |170|
02.0   |271|
03.0   | 72|
04.0   |113|
05.0   | 79|
11.0   | 77|
12.0   | 75|
12.1   | 23|
13.0   | 67|
13.1   |  7|
14.0   | 37|
15.0   | 21|
15.1   | 28|
16.0   |  8|
17.0   |  8|
*/

select e.version
, count(*) ct
from emojis e 
join categories c on e.category_id = c.id 
group by e.version
order by e.version;




