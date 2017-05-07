﻿---
date:  "2017-05-03T22:23:25+02:00"
title: "Using Macros"
---

You can learn about macros in the **SAS Macro Language 1: Essentials course**.

###Macro Program for Creating Box Plots for All of Predictor Variables

```
%let categorical=House_Style2 Overall_Qual2 Overall_Cond2 Fireplaces 
         Season_Sold Garage_Type_2 Foundation_2 Heating_QC 
         Masonry_Veneer Lot_Shape_2 Central_Air;
/* Macro Usage: %box(DSN = , Response = , CharVar = ) */
%macro box(dsn      = ,
           response = ,
           Charvar  = );
%let i = 1 ;
%do %while(%scan(&charvar,&i,%str( )) ^= %str()) ;
    %let var = %scan(&charvar,&i,%str( ));
    proc sgplot data=&dsn;
        vbox &response / category=&var 
                         grouporder=ascending 
                         connect=mean;
        title "&response across Levels of &var";
    run;
    %let i = %eval(&i + 1 ) ;
%end ;
%mend box;
%box(dsn      = statdata.ameshousing3,
     response = SalePrice,
     charvar  = &categorical);
title;
options label;
```

