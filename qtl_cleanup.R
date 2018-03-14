eqtl_raw <- read.table("Cells_EBV-transformed_lymphocytes.v7.egenes.txt", sep = "\t", header = TRUE)
meqtl_raw <- read.table("meQTL_raw.txt", sep = "\t", header = TRUE, row.names = NULL)

## need to uniquely label each entry
vect_1 <- c(rep("meqtl", times = nrow(meqtl_raw)))
vect_2 <- c(1:nrow(meqtl_raw))
meqtl_ID <- c()
for(i in 1:length(vect_1)){
  meqtl_ID[i] <- paste(vect_1[i],vect_2[i], sep = "_")
}
rownames(meqtl_raw) <- meqtl_ID
#########
# Colnames got shifted in meqtl input, need to fix
newnames <- colnames(meqtl_raw)[2:3]
meqtl_raw <- meqtl_raw[,-c(3)] ## don't run more than once
colnames(meqtl_raw)[1:2] <- newnames

## eqtl labeling
vect_1.1 <- c(rep("eqtl", times = nrow(eqtl_raw)))
vect_2.1 <- c(1:nrow(eqtl_raw))
eqtl_ID <- c()
for(i in 1:length(vect_1.1)){
  eqtl_ID[i] <- paste(vect_1.1[i],vect_2.1[i], sep = "_")
}
rownames(eqtl_raw) <- eqtl_ID

write.csv(x = eqtl_raw, file = "eqtl_gm12878_cleaned.csv")
write.csv(x = meqtl_raw, file = "meqtl_gm12878_cleaned.csv")





n <- read.csv("chromosome_all_qtl_map.csv", header = T, row.names = 1)
for(i in 1:nrow(n)){if(n[i,"pos_SNP"] == n[i,"methylSNP"]){print("me")}}



