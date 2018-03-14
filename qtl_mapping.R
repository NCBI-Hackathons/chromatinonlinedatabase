setwd()
## set your wd to your local git repo

eqtl_raw <- read.csv("eqtl_gm12878_cleaned.csv", row.names = 1, header = T)
meqtl_raw <- read.csv("meqtl_gm12878_cleaned.csv", row.names = 1, header = T)



temphold <- NULL
for(q in 1:22){ ## pick which chromosome number(s) you want to look at
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
      if(((meqtl_chr1[i,"PROBE_START"] < temp_start) && (temp_start < meqtl_chr1[i,"PROBE_END"])) | ((meqtl_chr1[i,"PROBE_START"] < temp_end) && (temp_end < meqtl_chr1[i,"PROBE_END"])) | (temp_start < (meqtl_chr1[i,"PROBE_START"]) && (meqtl_chr1[i,"PROBE_END"]< temp_end))){
        overlap[i] <- TRUE
      } else {
        overlap[i] <- FALSE
      }
    }
    if(sum(overlap) > 0){
      meqtl_row_vect <- rownames(meqtl_chr1)[overlap]
      for(b in 1:length(meqtl_row_vect)){
        holding1[index,"meqtl_i"] <- meqtl_row_vect[b]
        holding1[index, "eqtl_j"] <- rownames(eqtl_chr1)[j]
        index <- index + 1
      }
    }
  }  
  if(q == 1){
    temphold <- holding1
  } else {
    temphold <- rbind(temphold, holding1)
  }
}

for(u in 1:nrow(temphold)){
  eqtl_char <- temphold[u,"eqtl_j"]
  meqtl_char <- temphold[u,"meqtl_i"]
  if(u == 1){
    eqtl_start <- c()
    eqtl_end <- c()
    rsID_SNP <- c()
    pos_SNP <- c()
    chr_num <- c()
    methylSNP <- c()
    meqtl_start <- c()
    meqtl_end <- c()
  }
  eqtl_start[u] <- c(eqtl_raw[eqtl_char,c("gene_start")])
  eqtl_end[u] <- c(eqtl_raw[eqtl_char,c("gene_end")])
  rsID_SNP[u] <- as.character(eqtl_raw[eqtl_char,c("rs_id_dbSNP147_GRCh37p13")])
  chr_num[u] <- as.character(meqtl_raw[meqtl_char, c("SNP_CHR")])
  methylSNP[u] <- c(meqtl_raw[meqtl_char, c("SNP_POSITION")])
  meqtl_start[u] <- c(meqtl_raw[meqtl_char, c("PROBE_START")])
  meqtl_end[u] <- c(meqtl_raw[meqtl_char, c("PROBE_END")])
  pos_SNP[u] <- c(eqtl_raw[eqtl_char, c("pos")])
}
finaldf <- cbind(temphold, chr_num, pos_SNP, rsID_SNP, eqtl_start, eqtl_end, methylSNP, meqtl_start, meqtl_end)
write.csv(x = finaldf, file = "chromosome_all_qtl_map.csv")

