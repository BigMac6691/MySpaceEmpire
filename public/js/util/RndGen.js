var RND_MULTIPLER = 25214903917;
var RND_ADDEND = 11;
var RND_MAX = 4294967296; // 2^32

function RndGen(seed)
{
	this._seed = seed;
}

RndGen.prototype.nextInt = function()
{
	this._seed = (this._seed * RND_MULTIPLER + RND_ADDEND) % RND_MAX;
	
	return this._seed >>> 0;
}

RndGen.prototype.nextFloat = function()
{
	return this.nextInt() / RND_MAX;
}
