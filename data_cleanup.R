setwd(dir = "C:/Users/Nate Diehl/Dropbox/HUGin/chromatinonlinedatabase/")
## set your wd to your local git repo

eqtl_raw <- read.table("Cells_EBV-transformed_lymphocytes.v7.egenes.txt", sep = "\t", header = TRUE)
meqtl_raw <- read.table("meQTL_raw.txt", sep = "\t", header = TRUE, row.names = NULL)
