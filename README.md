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
   -  Yun Li lab:
      - https://yunliweb.its.unc.edu//HUGIn/

With this data, we hope to identify regions of the genome whose expression changes are controlled both by an Methyl-QTL as well as an eQTL. Previous analysis have identified SNPs that serve as both eQTLs and Methyl-QTLs, but we hoped to integrate this data with comprehensive Hi-C data from the Yun Li lab.
### Data cleaning
Upon downloading these files, we cleaned up the input with the [qtl_cleanup.R](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/qtl_cleanup.R) script. We apologize for the general lack of comments - we were rushed!


### Analysis and output
Analysis of these files were carried out in the [qtl_mapping.R](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/qtl_mapping.R) script. Once again, we apologize for the lack of comments and usability of these scripts - we were rushed!

Using these two data sets, we found overlaping regions of significant expression changes controlled by both an eQTL and an meQTL. These results for LCL cells are stored [here](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/chromosome_all_qtl_map.csv). 

Using this analysis, we chose one SNP from the eQTL data set and mapped it into the Hi-C dataset in an attempt to identify regions of the genome that are both connected by expression changes and are close in proximity.

Below, you see a figure with the SNP rs78002007 which was identified in significant association with a region of the genome upstream (green bar). This region overlapped with another region associated with a local methyl-QTL (blue arrow SNP, orange bar expression locus). 

![alt text](https://github.com/NCBI-Hackathons/chromatinonlinedatabase/blob/master/rs78002007.png "figure 1")


Our hope is to use this integrated "-OMICS" approach to identify novel mechanisms of gene regulation in complex 3-dimensional systems.


