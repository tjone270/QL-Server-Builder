import minqlx, requests, redis, time
from random import randint

class serverbuilder_node(minqlx.Plugin):
    def __init__(self):
        server = (self.get_cvar("qlx_redisAddress").split(":"))
        self.database = redis.Redis(host=server[0], port=server[1], db=15, password=self.get_cvar("qlx_redisPassword"))

        self.set_cvar("sv_master", "0")
        self.set_cvar("qlx_serverBrandTopField", "Powered by ^4TomTec Solutions^7 Quake Live server hosting and management technologies.")
        self.set_cvar("qlx_serverBrandBottomField", "Visit ^2serverbuilder.thepurgery.com^7 to make your own Quake Live server.")
        
        self.add_command("getinfo", self.cmd_getinfo, 0)
        
        self.add_hook("player_connect", self.handle_player_connect)
        self.add_hook("player_loaded", self.handle_player_loaded)
        self.add_hook("player_disconnect", self.handle_player_disconnect)

        self.server_id = "server_" + self.get_cvar("sv_identifier")
        self.server_location = str.replace(self.get_cvar("sv_location"), " ", "-")
        self.server_key = self.server_location.lower() + ":" + self.server_id
        
        self.is_ready = False
        self.isFirstPlayer = True
        self.mapSet = False

        for key in (self.database.keys("{}:*".format(self.server_key))):
            self.database.delete(key)
    
        self.initialise()


    def initialise(self):
        self.database.set("{}:receiving".format(self.server_key), "1")
        self.checkForConfiguration()

    @minqlx.thread
    def checkForConfiguration(self):
        while True:
            time.sleep(3)
            try:
                active = bool((self.database.get("{}:active".format(self.server_key))).decode())
            except:
                active = False
            if active:
                self.database.set("{}:received".format(self.server_key), "1")
                self.database.set("{}:receiving".format(self.server_key), "0")
                break

        self.configureServer(self.getCvars())
        
    def configureServer(self, config):
        cvars = self.getCvars()
        for cvar, value in cvars.items():
            self.set_cvar(cvar, value)

        for plugin in (self.database.smembers("{}:plugins".format(self.server_key))):
            minqlx.load_plugin(plugin.decode())

        self.set_cvar("sv_master", "1")
        
        self.is_ready = True

    def getCvars(self):
        cvars = list(self.database.smembers("{}:cvars".format(self.server_key)))
        cvardict = dict()
        for cvar in cvars:
            value = self.database.get("{}:cvar:{}".format(self.server_key, cvar.decode()))
            cvardict[cvar.decode()] = value.decode()

        return cvardict

    def handle_player_connect(self, player):
        if str(player.steam_id)[0] == "9": return
        if not self.is_ready:
            return "^{}http://serverbuilder.thepurgery.com\n".format(randint(0,7))
        else:
            if not self.mapSet:
                theMap = self.database.get("{}:map".format(self.server_key)).decode()
                theFactory = self.database.get("{}:factory".format(self.server_key)).decode()
                self.change_map(theMap, theFactory)
                cvars = self.getCvars()
                for cvar, value in cvars.items():
                    self.set_cvar(cvar, value) # we set them all again incase the factory overrode some cvars
                self.mapSet = True

    def handle_player_loaded(self, player):
        if str(player.steam_id)[0] == "9": return
        if self.isFirstPlayer:
            player.tell("^2Info:^7 Welcome to your server.")
            player.tell("^2Info:^7 As soon as there's no more people connected to this server, it'll shut down automatically.")
            player.addmod()
            self.isFirstPlayer = False

    @minqlx.next_frame
    def handle_player_disconnect(self, player, reason):
        if (reason == "was kicked") and (str(player.steam_id)[0] == "9"): return # is testingbotsupport bot
        if len(self.players()) <= 1:
            self.destroySession()
                    
    def destroySession(self):
        for key in (self.database.keys("{}:*".format(self.server_key))):
                self.database.delete(key)

        minqlx.console_command("quit")

    def cmd_getinfo(self, player, msg, channel):
        cvardict = self.getCvars()
        for cvar, value in cvardict.items():
            self.msg("^1Debug:^7 CVAR: ^2{}^7 => ^2{}^7.".format(cvar, value))

        for plugin in (self.database.smembers("{}:plugins".format(self.server_key))):
            self.msg("^1Debug:^7 Plugin: ^2{}^7 loaded.".format(plugin.decode()))

        self.msg("^1Debug:^7 Map: ^2{}^7.".format(self.database.get("{}:map".format(self.server_key)).decode()))
        self.msg("^1Debug:^7 Factory: ^2{}^7.".format(self.database.get("{}:factory".format(self.server_key)).decode()))
