Forum Module v2.0.0-dev

### Native Forum Module for PyroCMS

#### PyroCMS Compatibility
3.0   - No 
2.3   - No (It's dead Jim)
2.2.x - Yes
2.1.x - Possibly (please report)
< 2.1.x - Unknown, probably not.


#### Maintaining Author 

visit: https://github.com/wbc-mike/pyro_module_forums
Issues: https://github.com/wbc-mike/pyro_module_forums/issues
Contact: http://enlivenapp.com/contact

#### Contributors
* Marko Gruter


Interested in contributing?  Open an issue tracker or submit a pull request. (don't forget to add your name to this file)


#### Installation

  - Download to local disk
  - decompress and name resulting folder "forums" (Optional,  recompress and save file as forums.zip)
  - place in addons/<site_ref>/modules/  OR addons/shared_addons/modules/ OR (if recompressed) upload via control panel
  - install via admin panel.

#### ToDo
  x (Done) (Implemented) Settings: allow access to forums for logged in/not logged in as chosen by site admin
  x (Done) (Not *completely* implemented) Convert to streams (done by Mark)
  x (Done) (Implemented) (Only Basic is complete) Update views to use Bootstrap and/or other CSS (IE: lose the tables layout)
  x (NOPE) Drag and drop to reorder categories and Forums (Currently, first in = top of list, last in = bottom of list)
  x (Done) (Needs implemtation) Add moderator functionality 
  x (Done) (Needs file purging) loose latex and bbcode
  x (Done) (Some Bugs) add markdown support


#### Change Log 
v2.0.0-dev - Currently under developement
 - Converted to Streams
 - Added permissions for Mods
 - Dropped BBCode and LaTeX
 - Implemented Markdown (replaces BBCode and LaTeX)
 - Installed front end framework (Basic, Bootstrap 3, etc)
 - Setting now allow choice of any public user or only logged in users to see forums


v1.5.1 - 5/13/14
  - Update this file
  - bug fixes / display on Bootstrap 3
  - User must be logged in to see forum now
  - added "report" post functionality (now emails site admin (contact_email))

v1.5.0 - 9/9/13
  - Inital release on this fork.
  - updated to play nice with PyroCMS 2.2.x
    - removed $this->data
    - a few other little thing that were done pre-v2 that was depreciated



Update from v1.0 for PyroCMS 2.1.x


### Original Readme and Author information

----
PyroCMS v2.1.x Forum Module - 07-07-2012
visit: http://www.cavaencoreparlerdebits.fr

----
PyroCMS v1.3.2 Forum Module
The old PyroCMS v0.9.9.7 Forum upgrade

For more info contact us: 
http://semicolondev.com/contact
 