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

## Website Improvements
Most of the work on Monday was devoted to creating a menu icon that allowed the user to select specific graphical parameters. Prior to these updates, upon changing one parameter the user would be automatically scrolled down to the updated figure on the web page. This prevented the user from conveniently altering multiple graphical parameters.

Old Menu:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/oldsite_1.PNG "Old Menu")

Updated Menu:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/hackathon2.png "New Menu")

Updated Graph Representation:
![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/hackathon1.png "New Graphs")

## Integration meQTL, eQTL, and Hi-C Data
Previous studies have shown that meQTL and eQTL data can be effectively integrated (https://doi.org/10.1371/journal.pgen.1004663). Combined with the experience of the Yun Li lab in analyzing Hi-C data and with the outstanding website improvements, we sought to integrate Hi-C analysis with both meQTL and eQTL data sets. 

### Our Workflow
We have identified and stored eQTL and meQTL datasets pertaining to the lymphoblastoid cell line (LCL) available from publicly available sources.
   - GTEx: https://www.gtexportal.org/home/
      - https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/eqtl_gm12878_cleaned.csv
   - Yoav Gilad lab: http://giladlab.uchicago.edu/data/meQTL_summary_table.txt
      - https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/meqtl_gm12878_cleaned.csv 
   -  

With this data, we hope to identify regions of the genome whose expression changes are controlled both by an Methyl-QTL as well as an eQTL. Previous analysis have identified SNPs that serve as both eQTLs and Methyl-QTLs, but we hoped to 

