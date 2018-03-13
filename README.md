# chromatinonlinedatabase
An Online Database of 3D Chromatin Structure

# Goal of Team HUGin
[alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/website_header_1.PNG "Old Website Header")

## Work Flow
1. Understand js to load the graphs on HUGin 
2. Create table to store bookmarks 
   1. parameters to load user settings
   2. table will be created by Tong (Yun Li lab)
3. adjust overall design of website
   1. use Bootstrap
4. Adjust menu functionality (js/jquery)

## Monday progress
1. Creating a new table to hold parameters
   - ID (int, PK)
     - will autoincrement
   - Name (varchar, 50)
   - Param (varchar(max))
      - used 1000 rather than maximum
2. Using FontAwesome

## Tuesday morning meeting
1. Most of the graphical parameters are being stored as sessions
   - instead, we'll build the graphical parameters into the URL
   - won't need to build a hidden database of parameters instead

