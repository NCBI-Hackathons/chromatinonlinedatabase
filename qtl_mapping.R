setwd(dir = "C:/Users/Nate Diehl/Dropbox/HUGin/chromatinonlinedatabase/")
## set your wd to your local git repo

eqtl_raw <- read.csv("eqtl_gm12878_cleaned.csv", row.names = 1, header = T)
meqtl_raw <- read.csv("meqtl_gm12878_cleaned.csv", row.names = 1, header = T)


## Start with chromosome 1
eqtl_chr1 <- eqtl_raw[eqtl_raw[,"gene_chr"] == 1,]
meqtl_chr1 <- meqtl_raw[meqtl_raw[,"PROBE_CHR"] == "chr1",]
meqtl_i <- c()
eqtl_j <- c()
holding1 <- data.frame(meqtl_i, eqtl_j)
index = 1
for(j in 1:nrow(eqtl_chr1)){
  pract <- eqtl_chr1[j,]
  temp_start <- pract[,"gene_start"]
  temp_end <- pract[,"gene_end"]
  overlap <- c()
  for(i in 1:nrow(meqtl_chr1)){
    if(((meqtl_chr1[i,"PROBE_START"] < temp_start) && (temp_start < meqtl_chr1[i,"PROBE_END"])) | ((meqtl_chr1[i,"PROBE_START"] < temp_end) && (temp_end < meqtl_chr1[i,"PROBE_END"]))){
      overlap[i] <- TRUE
    } else {
      overlap[i] <- FALSE
    }
  }
  if(sum(overlap) > 0){
    meqtl_row_vect <- rownames(meqtl_chr1)[overlap]
    holding1[index,"meqtl_i"] <- meqtl_row_vect
    holding1[index, "eqtl_j"] <- rownames(eqtl_chr1)[j]
    index <- index + 1
  }
  if(q == 1){
    temphold <- holding1
  } else {
    temphold <- rbind(temphold, holding1)
  }
}

temphold <- NULL
for(q in 15:22){
  num <- paste("chr",q, sep = "")
  eqtl_chr1 <- eqtl_raw[eqtl_raw[,"gene_chr"] == q,]
  meqtl_chr1 <- meqtl_raw[meqtl_raw[,"PROBE_CHR"] == num,]
  meqtl_i <- c()
  eqtl_j <- c()
  holding1 <- data.frame(meqtl_i, eqtl_j)
  index <- 1
  for(j in 1:nrow(eqtl_chr1)){
    pract <- eqtl_chr1[j,]
    temp_start <- pract[,"gene_start"]
    temp_end <- pract[,"gene_end"]
    overlap <- c()
    for(i in 1:nrow(meqtl_chr1)){
      if(((meqtl_chr1[i,"PROBE_START"] < temp_start) && (temp_start < meqtl_chr1[i,"PROBE_END"])) | ((meqtl_chr1[i,"PROBE_START"] < temp_end) && (temp_end < meqtl_chr1[i,"PROBE_END"]))){
        overlap[i] <- TRUE
      } else {
        overlap[i] <- FALSE
      }
    }
    if(sum(overlap) > 0){
      meqtl_row_vect <- rownames(meqtl_chr1)[overlap]
      holding1[index,"meqtl_i"] <- meqtl_row_vect
      holding1[index, "eqtl_j"] <- rownames(eqtl_chr1)[j]
      index <- index + 1
    }
  }  
  if(q == 1){
    temphold <- holding1
  } else {
    temphold <- rbind(temphold, holding1)
  }
}

