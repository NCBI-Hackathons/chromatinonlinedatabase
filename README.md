# Team HUGin
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/website_header_1.PNG "Old Website Header")
An out-of-date online database of 3D chromatin structure, in need of user-friendly updates and improvements in functionality.


## Our Goal
During this Hackathon, we aim to overhaul the layout and interface of the HUGin website to drastically improve the user experience. In addition, we plan to improve the reproducibility of the graphics produced by the software by removing parameters stored as sessions and moving graphical parameters to unique URLs.


## Work Flow
1. Understand js to load the graphs on HUGin 
2. Create table to store bookmarks 
   1. parameters to load user settings
   2. table will be created by Tong (Yun Li lab)
3. adjust overall design of website
   1. use Bootstrap
4. Adjust menu functionality (js/jquery)

## Monday progress
Most of the work on Monday was devoted to creating a menu icon that allowed the user to select specific graphical parameters. Prior to these updates, upon changing one parameter the user would be automatically scrolled down to the updated figure on the web page. This prevented the user from conveniently altering multiple graphical parameters.

Old Menu:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/oldsite_1.PNG "Old Menu")

Updated Menu:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/hackathon2.png "New Menu")

Updated Graph Representation:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/hackathon1.png "New Graphs")

## Tuesday WIP
1. Parameters were previously stored as cookies/sessions
   - sorting through php script to identify all parameters stored as cookies

Plans for Tuesday afternoon/Wednesday:
- Provide Yun Li lab with further suggestions for web development
- Switch directions
   - :)

