import minqlx, time
from random import randint

class serverbuilder_helper(minqlx.Plugin):
    def __init__(self):
        self.add_hook("map", self.handle_map)
        self.add_hook("game_countdown", self.handle_game_countdown)

        self.set_cvar_once("qlx_infiniteAmmo", "0") # 0=normal ammo, 1=always, 2=during warmup only

        
    @minqlx.next_frame   
    def handle_map(self, mapname, factory):
        # turn on infinite ammo for warm-up
        if self.get_cvar("qlx_infiniteAmmo", int) != 0:
            self.set_cvar("g_infiniteAmmo", "1")

    def handle_game_countdown(self):
        self.play_sound("sound/items/protect3.ogg")
        if self.get_cvar("qlx_infiniteAmmo", int) == 2:
            self.set_cvar("g_infiniteAmmo", "0")
