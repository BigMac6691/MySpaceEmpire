MySpaceEmpire
=============
Basic turn based strategy game set in space.

Using HTML5, SVG, jscolor-1.4.2, rainforestnet.com datetimepicker 2.2.4

TODO:
1	Keep track of all orders during player game play
2	Send new orders to server via AJAX - limit change to just the new order (game id, user id, type, source)
	2.1	Build orders are OK, each instance of a type industry has its own unique industry id that would appear in the source 
3	New orders are processed on server by first deleting all related orders then inserting the new ones
4	If user changes orders they are still sent to server as above

5	Drop ship type and replace with a build type where the attributes of what is being built are ina column that a JSON object in string form
6	Create a relational table to connect industry type with build type so that a given build type can be built by more than one industry

{mass:15,ltube:1,mtube:0,htube:0,ctube:0,pdl:1,fbay:0,armour:0,move:1,cargo:0}
{mass:600,ltube:8,mtube:6,htube:0,ctube:6,pdl:6,fbay:0,armour:6,move:1,cargo:260}
{mass:2500,ltube:16,mtube:12,htube:0,ctube:12,pdl:12,fbay:0,armour:21,move:2,cargo:550}
{mass:15000,ltube:0,mtube:15,htube:10,ctube:18,pdl:18,fbay:0,armour:150,move:2,cargo:2500}
{mass:45000,ltube:0,mtube:20,htube:30,ctube:30,pdl:30,fbay:0,armour:450,move:2,cargo:5000}
{mass:33000,ltube:0,mtube:8,htube:0,ctube:30,pdl:30,fbay:36,armour:330,move:2,cargo:2000}
{mass:900,ltube:0,mtube:0,htube:0,ctube:0,pdl:2,fbay:0,armour:9,move:2,cargo:500}
{mass:5000,ltube:0,mtube:0,htube:0,ctube:0,pdl:4,fbay:0,armour:5,move:1,cargo:3000}
{mass:15000,ltube:0,mtube:0,htube:0,ctube:0,pdl:8,fbay:0,armour:15,move:2,cargo:10000}
{mass:4000,ltube:4,mtube:0,htube:0,ctube:8,pdl:8,fbay:3,armour:60,move:0,cargo:2000}
{mass:8000,ltube:0,mtube:16,htube:0,ctube:32,pdl:32,fbay:6,armour:120,move:0,cargo:4000}
{mass:16000,ltube:0,mtube:0,htube:32,ctube:64,pdl:64,fbay:12,armour:240,move:0,cargo:8000}
{mass:32000,ltube:0,mtube:0,htube:52,ctube:104,pdl:104,fbay:24,armour:480,move:0,cargo:16000}
{mass:64000,ltube:0,mtube:0,htube:76,ctube:152,pdl:152,fbay:48,armour:960,move:0,cargo:32000}
{mass:15000,ltube:0,mtube:0,htube:0,ctube:8,pdl:16,fbay:0,armour:75,move:0,cargo:10000}

{cost:100} - industry
{cost:1000} - shipyard



{mass:15,ltube:1,pdl:1,move:1}
{mass:600,ltube:8,mtube:6,ctube:6,pdl:6,armour:6,move:1,cargo:260}
{mass:2500,ltube:16,mtube:12,ctube:12,pdl:12,armour:21,move:2,cargo:550}
{mass:15000,mtube:15,htube:10,ctube:18,pdl:18,armour:150,move:2,cargo:2500}
{mass:45000,mtube:20,htube:30,ctube:30,pdl:30,armour:450,move:2,cargo:5000}
{mass:33000,mtube:8,ctube:30,pdl:30,fbay:36,armour:330,move:2,cargo:2000}
{mass:900,pdl:2,armour:9,move:2,cargo:500}
{mass:5000,pdl:4,armour:5,move:1,cargo:3000}
{mass:15000,pdl:8,armour:15,move:2,cargo:10000}
{mass:4000,ltube:4,ctube:8,pdl:8,fbay:3,armour:60,move:0,cargo:2000}
{mass:8000,mtube:16,ctube:32,pdl:32,fbay:6,armour:120,move:0,cargo:4000}
{mass:16000,htube:32,ctube:64,pdl:64,fbay:12,armour:240,move:0,cargo:8000}
{mass:32000,htube:52,ctube:104,pdl:104,fbay:24,armour:480,move:0,cargo:16000}
{mass:64000,htube:76,ctube:152,pdl:152,fbay:48,armour:960,move:0,cargo:32000}
{mass:15000,ctube:8,pdl:16,armour:75,move:0,cargo:10000}
