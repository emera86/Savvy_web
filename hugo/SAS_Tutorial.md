

# SAS: statistical analysis systems

## General stuff

## Accessing data

# SAS Programming Essentials

##Getting Started with SAS Programming
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m411/m411_5_a_sum.htm)

##Working with SAS Programs
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m412/m412_3_a_sum.htm)

***Comments***

```
/* comment */
* comment statement;
```

## Accessing data
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m413/m413_3_a_sum.htm)

### Accessing SAS libraries

* **libref**: library reference name (shortcut to the physical location). There are three rules for valid librefs:
* A length of one to eight characters
* Begin with a letter or underscore
* The remaining characters are letters, numbers, or underscores
* Valid variable names begin with a letter or underscore, and continue with letters, numbers, or underscores. The **VALIDVARNAME** system option specifies the rules for valid SAS variable names that can be created and processed during a SAS session: 

```
OPTIONS VALIDVARNAME=V7 (default) | UPCASE | ANY;
```

* **libref.data-set-name**: data set reference two-level name
* **data-set-name**: when the data set belongs to a temporary library, you can optionally use a one-level name (SAS assumes that it is contained in the **work** library, which is the default)
* The **LIBNAME** statement associates the **libref** with the physical location of the library/data for the current SAS session

```
LIBNAME libref-name 'SAS-library-folder-path' <options>;
```

*Example*
```
%let path=/folders/myfolders/ecprg193; 
libname orion "&path";`
```

* To erase the association between SAS and a custom library

```
LIBNAME libref-name CLEAR;
```

* To check the **contents of a library** programatically

```
PROC CONTENTS DATA=libref._ALL_;
RUN;`
```

* To hide the descriptors of all data sets in the library (it could generate a very long report) you can add the option **nods** (only compatible with the keybord **\_all_**)

```
PROC CONTENTS DATA=libref._ALL_ NODS;
RUN;
```

* To access a data set you can use a **proc print** step

```
PROC PRINT DATA=SAS-data-set;
RUN;
```

### Examining SAS data sets

Parts of a library (SAS notation):

* Table = **data set**
* Column = **variable**
* Row = **observation**
 
The **descriptor portion** (PROC CONTENTS) contains information about the attributes of the data set (metadata), including the variable names. It is show in three tables:

* Table 1: general information about the data set (name, creation date/time, etc.)
* Table 2: operating environment information, file location, etc.
* Table 3: alphabetic list of variables in the data set and their attributes

The **data portion** (PROC PRINT) contains the data values, stored in variables (numeric/character)

* Numeric values: right-aligned
* Character values: left-aligned
* **Missing values**: ***blank*** for character variables and ***period*** for numeric ones. To change this default behaviour use  `MISSING='new-character'`
* Valid **character values**: letters, numbers, special characters and blanks
* Valid **numeric values**: digits 0-9, minus sign, single decimal point, scientific notation (E)
* Values length: for character variables 1 byte = 1 character, numeric variables have 8 bytes of storage by default (16-17 significant digits)
* Other attributes: **format**, **informat**, **label**

## Producing detailed reports
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m415/m415_4_a_sum.htm)

### Subsetting Report Data

```
PROC PRINT DATA=SAS-data-set(OBS=3) NOOBS;  /* OBS=3 prints only 3 elements | NOOBS hides the 'Obs' */
    VAR variable1 variable2 variable3;      /* prints out only this variables in the report */
    SUM variable1 variable2;                /* adds an extra line at the end with the total */
    WHERE variable3<1000; variable3<1000;   /* operators: < > <= >= = ^= in + - / * ** & | ~ ^ ? */
    WHERE variable4 in ('Child','Elder');   /* only the last WHERE condition is applied */
    WHERE variable1=20 AND variable4 CONTAINS 'case-sensitive-substring';  /* CONTAINS = ? */
    IDWHERE ANYALPHA(variable) NE 0         /* only values containing at least a letter */
    ID variable1                            /* replaces the 'Obs' column by a selected variable values */
    BY variable3variable3                   /* separate in different tables for different variable values (sort first) */
RUN;
```

Special **WHERE operators**:

* **BETWEEN x AND y**: an inclusive range
* **WHERE SAME AND**: augment a previous where expression (both applied)
* **IS NULL**: a missing value
* **IS MISSING**: a missing value
* **LIKE**: matches a pattern (% = any number of characters, _ = one character). E.g.: 'T_m%'
* The **SOUNDS-LIKE (=\*)** operator selects observations that contain a spelling variation of a specified word or words. This operator uses the *Soundex* algorithm to compare the variable value and the operand.
* [**ANYVALUE**](http://support.sas.com/documentation/cdl/en/lrdict/64316/HTML/default/viewer.htm#a002194060.htm) is an interesting function that searches a character string for an alphabetic character, and returns the first position at which the character is found


**Note:** To compare with a SAS date value you need to express is as a SAS date constant: **'DDMM<\YY>YY'D**

### Sorting and Grouping Report Data

```
PROC SORT DATA=SAS-data-set
    OUT=new-SAS-data-set NODUPKEY;                                           /* optional */
    DUPOUT=work.duplicates;                                                  /* optional */
    BY ASCENDING variable1-to-be-sorted DESCENDING variable2-to-be-sorted;   /* optional (ASCENDING is the default order)*/
RUN;`
```

* The **NODUPKEY** option deletes observations with duplicate **BY** values
* **DUPOUT** writes duplicate observations to a separate output data set

### Enhancing Reports

```
TITLEline 'text';       
FOOTNOTEline 'text';`

TITLE1 'text1';
TITLE1 'text1_change';     /* Change title text and also cancels all footnotes with higher numbers */
TITLE;                     /* Cancel (erase) all titles */
```

* The **lines** specifies the line (1-10) on which the title/footnote will appear (line = 1 is the default value)
* The title/footnote will remain until you **change** it, **cancel** it or you **end your SAS session**

---

Assigning **temporary labels** to display in the report instead of the variable names:

```
PROC PRINT DATA=SAS-data-set LABEL;           /* you need to add the LABEL option to display the labels */ 
    LABEL variable1 = 'new variable1 name' 
          variable2 = 'new variable2 name';
    LABEL variable3 = 'new variable3 name';
RUN;
```

- The **LABEL** lengths can go up to 256 characters long
- You can specify several labels in one **LABEL** statement or use a separate **LABEL** statement for each variable

```
PROC PRINT DATA=SAS-data-set SPLIT='*';            /* you no longer need to add the LABEL option, SPLIT does the same work */ 
    LABEL variable1 = 'new variable1*long name';   /* the variable name ocuppies 2 lines now */
RUN;
```

## Formatting data values
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m416/m416_3_a_sum.htm)

### Using SAS formats

```
PROC PRINT DATA=SAS-data-base;
    FORMAT FORMAT variable1 variable2 format;
    FORMAT variable3 format3 variable4 format4;
RUN;
```

Format definition: **<\$>*format*<\w>.<\d>**

* **<\$>** = character format
* ***format*** = format name
* **<\w>** = total width (includes special characters, commas, decimal point and decimal places)
* **.** = required syntax (dot)
* **<\d>** = decimal places (numeric format)

SAS formats ([Dictionary of formats](http://support.sas.com/documentation/cdl/en/leforinforref/64790/HTML/default/viewer.htm#p0z62k899n6a7wn1r5in6q5253v1.htm)):

* **\$w.** = writes standard character data
* **\$UPCASE.** = writes a string in uppercase
* **\$QUOTE.** = writes a string in quotation marks 
* **w.d** = writes standard numeric data
* **COMMAw.d** = writes numeric values with a comma that separates every three digits and a period that separates the decimal fraction
* **DOLLARw.d** = writes numeric values with a leading dollar sign, a comma that separates every three digits and a period that separates the decimal fraction
* **COMMAXw.d** = writes numeric values with a period that separates every three digits and a coma that separates the decimal fraction
* **EUROXw.d** = writes numeric values with a leading euro symbol, a period that separates every three digits and a comma that separates the decimal fraction

SAS date values: **MMDDYY<\w>.** / **DDMMYY<\w>.** / **MONYY<\w>.** / **DATE<\w>.** / **WEEKDATE.**
* w = 6: only date numbers
* w = 8: date numbers with **/** separators (just the last 2 digits of year)
* w = 10: date numbers with **/** separators (full 4-digit year)

**Note:** dates before 01/01/1960 (0 value) will appear as negative numbers

### Creating and applying user-defined formats

```
PROC FORMAT;
	VALUE <$>format-name value-or-range1='formatted-value1'
                         value-or-range2='formatted-value2';
RUN;`
```

```
PROC PRINT DATA=SAS-data-set;
    FORMAT variable1 <$>format-name.;
RUN;
```

* A format name can have a maximum of **32 characters**
* The name of a format that applies to **character values** must begin with a **dollar sign** followed by a letter or underscore
* The name of a format that applies to **numeric values** must begin with a letter or underscore
* A format name cannot end in a number
* All remaining characters can be letters, underscores or numbers
* A user defined format name cannot be the name of a SAS format

Each **value-range set** has three parts:

* **value-or-range**: specifies one or more values to be formatted (it can be a value, a range or a list of values)
* **=**: equal sign
* **formatted-value**: the formatted value you want to display instead of the stored value/s (it is allways a character string no matter wheter the format applies to character values or numeric values)

```
PROC FORMAT LIBRARY = my-format-library;   /* To save the custom formats */
    VALUE string 'A'-'H'='First'
                 'I','J','K'='Middle'
                  OTHER = 'End';           /* Non-specified values */
    VALUE tiers low-<50000='Tier1'         /* 50000 not included */
                50000-<100000='Tier2'      /* 100000 not included */
                100000-high='Tier3'
                .='Missing value';
RUN;
```

**Note1:** if you omit the **LIBRARY** option, then formats and informats are stored in the **work.formats** catalog

**Note2:** if you do not includ the keyword **OTHER**, then SAS applies the format only to values that match the value-range sets that you specify and the rest of values are displayed as they are stored in the data set

**Note3:** you can only use the **<** symbol to define a non-inclusive range.

```
OPTIONS FMTSEARCH = (libref1 libref2... librefn)
```

* The **FMTSEARCH** system option controls the order in which format catalogs are searched until the desired member is found.
* The **WORK.FORMATS** catalog is always searched first, unless it appears in the **FMTSEARCH** list. 

## Reading SAS data sets
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m417/m417_4_a_sum.htm)

To create a new data set that is a subset of a previous data set:

```
DATA output-SAS-data-set;
	SET input-SAS-data-set;
    WHERE where-expression;
    variable_name	WHERE where-expression;
	variable_name = expression;     /* new variable creation */
RUN;
```

**Note1:** if a missing value is involved in an arithmetic calculation the result will be a missing value too

**Note2:** new variables being created in the DATA step and not contained in the original data set cannot be used in a WHERE statement

### Customizing a SAS data set

How to select a subset of the variables/observations of the original data set:

```
DATA output-SAS-data-set;
    SET input-SAS-data-set;
    DROP variable-list;        /* original variables to exclude */
    KEEP variable-list;        /* original variables to include + new variables */
RUN;
```

How SAS processes the **DATA** step:

**Compilation phase**

- SAS scan each DATA step statement for syntax errors and converts the program into machine code if everything's alright. 
- SAS also creates the program data vector (**PDV**) in memory to hold the current observation.
 - **\_N\_**: iteration number of the DATA step
 - **\_ERROR\_**: its value is 0 is there are no errors (1 if there are some)
- SAS creates the descriptor portion of the new data set (takes the original one, adds the new variables and flags the variables to be dropped). 

**Execution phase**

- SAS initializes the PDV to missing
- SAS reads and processes the observations from the input data set 
- SAS creates observations in the data portion of the output data set (an implicit output/implicit return loop over all the observations that continues until EOF)

---

Subsetting **IF** statement: 

```
DATA output-SAS-data-set;
	SET input-SAS-data-set;
    IF expression;
RUN;
```

* When the expression is false, SAS excludes the observation from the output data set and continues processing
* While original values can be managed with a **WHERE** statement as well as an **IF** statement, for **new variable** conditionals only **IF** can be used
* You should subset as early as possible in your program for more efficient processing (a **WHERE** before an **IF** can make the processing more efficient).
* In a **PROC** step **IF** statements are **NOT allowed**

---

Subsetting **IF-THEN/DELETE** statement: 

```
DATA output-SAS-data-set;
	SET input-SAS-data-set;
	IF expression1 or expression2 then delete;
RUN;
```

* The **IF-THEN/DELETE** statement eliminates the observations where the **conditions are not met** (on the contrary of what the **IF** does)
* The **DELETE** statement stops processing the current observation. It is often used in a THEN clause of an IF-THEN statement or as part of a conditionally executed DO group.

---

Addition of several variables: **Total=sum(var1, var2, var3)**
Count of nonmissing values: **Nonmissing=n(var1, var2, var3)**

### Adding permanent attributes

***Permanent variable labels***

```
DATA output-SAS-data-set;
	SET input-SAS-data-set;
    LABEL variable1='label1'
          variable2='label2';
RUN;
```

```
PROC PRINT DATA=output-SAS-data-set label;
RUN;
```

* If you use the **LABEL** statement in the **PROC** step the labels are **temporary** while if you use it in the **DATA** step, SAS **permanently** associates the labels to the variables
* Labels and formats that you specify in **PROC** steps override the permanent labels in the current step. However, the permanent labels are not changed.

***Permanent variable formats***

```
DATA output-SAS-data-set;
    SET	SET input-SAS-data-set;
    FORMAT variable1 format1
           variable2 format2;
RUN;
```

## Reading spreadsheet and database data
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m418/m418_3_a_sum.htm)

### Reading spreadsheet data

To determine the SAS products that are included in your SAS license, you can run the following PROC SETINIT step:

```
PROC SETINIT;
RUN;
```

---

SAS/ACCESS LIBNAME statement (read/write/update data):

```
LIBNAME libref <engine> <PATH=>"workbook-name" <options>;
```

E.g.:<br>
**Default engine:** `LIBNAME orionx excel "&path/sales.xls"`<br>
**PC Files server engine:** `LIBNAME orionx pcfiles PATH="&path/sales.xls"`<br>

- **<\engine>**: excel (if both SAS and Office are 32/64 bits), pcfiles (if the value is different)
- The icon of the library will be different (a globe) indicating that the data is outside SAS
- The members whose name ends with a **\$** are the **spreadsheets** while the others are named **ranges**. In case it has the **\$**, you need to refer to that Excel worksheet in a special way to account for that special character (SAS name literal): `libref.'worksheetname\$'n`
- You can use the **`VALIDVARNAME = v7`** option in SAS Enterprise Guide to cause it to behave the same as in the SAS window environment
- Is important to disassociate the library: the workbook cannot be opened in Excel meanwhile (SAS puts a lock on the Excel file when the libref is assigned): **`LIBNAME libref CLEAR;`**

---

Import the xls data:

```
PROC IMPORT DATAFILE="/folders/myfolders/reading_test.xlsx"
            OUT=work.myexcel
            DBMS=xlsx 
            REPLACE;
RUN;
```

### Reading database data

```
LIBNAME libref engine <SAS/ACCESS options>;
```

- **engine**: oracle or BD2
- **SAS/ACCESS options**: USER, PASSWORD/PW, PATH (specifies the Oracle driver, node and database), SCHEMA (enables you to read database objects such as tables and views)

## Reading raw data files
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m419/m419_5_a_sum.htm)

### Introduction to reading raw data files

- **Raw data files** are not software specific
- A **delimited raw data file** is an external text file in which the values are separated by spaces or other special characters.
- A **list input** will be used to work with delimited raw data files that contain standard and/or nonstandard data
- **Standard data** is data that SAS can read without any special instructions
- **Nonstandard data** includes values like dates or numeric values that include special characters like dollar signs (extra instructions needed)

### Reading standard delimited data

```
DATA output-SAS-data-set-name;
	LENGTH variable(s) <$> length;
	INFILE 'raw-data-file-name' DLM='delimiter';  
	INPUT variable1 <$> variable2 <$> ... variableN <$>;    /* $ = character variables */     
RUN;
```

**E.g.:**<br>

```
DATA work.sales1;
	LENGTH First_Name Last_Name $ 12 Gender $ 1;
	INFILE '&path/sales.csv' DLM=',';  
	INPUT Employee_ID Gender $ Salary $ Job_Title $ Country $; 
RUN;
```

- With **list input**, the default length for all variables is 8 bytes
- SAS uses an **input buffer** only if the input data is a raw data file
- The variable names will appear in the report as stated in the **LENGTH** statement (watch out the uppercase/lowercase)
- The **LENGTH** statement must precede the **INPUT** statement in order to correctly set the length of the variable
- The variables not specified in the **LENGTH** statement will appear at the end of the table. If you want to keep the original order you should include all variables even if you want them to have the defaul length (8)


### Reading nonstandard delimited data

You can use a **modified list input** to read all of the fields from a raw data file (including nonstandard variables)

- Informats are similar to formats except that **formats** provide instruction on how to **write** a value while **informats** provide instruction on how to **read** a value
- The **colon format modifier (:)** causes SAS to read up to the delimiter

```
INPUT variable <$> variable <:informat>;
```

**E.g.:**<br>
```
:date.
:mmddyy.
```


- **COMMA./DOLLAR.**: reads nonstandard numeric data and removes embedded commas, blanks, dollar sign, percent signs and dashes
- **COMMAX./DOLLARX.**: reads nonstandard numeric data and removes embedded non-numeric characters; reverses the roles of the decima point and the comma
- **EUROX.**: reads nonstandard numeric data and removes embedded non-numeric characters in European currency
- **\$CHAR.**: reads character values and preserves leading blanks
- **\$UPCASE.**: reads character values and converts them to uppercase

---

- You cannot use a **WHERE** statement when the input data is a raw data file instead of a SAS data set

---

```
DATA (...);
	INFILE DATALINES DLM=',';   /* only if datalines are delimited */
    INPUT (...);
    DATALINES;
    <instream data>
    ;`
 	INPUT (...);
	DATALINES;
<instream data>
;
```

- The null statement (**;**) indicates the end of the input data
- You precede the instream data with the *DATALINES* statement and follow it with a null statement
- The instream data should be the **last part of the DATA step** except for a null statement

**E.g.:**<br>

```
DATA work.managers;
   infile datalines dlm='/';
   input ID First :$12. Last :$12. Gender $ Salary :comma. 
            Title :$25. HireDate :date.;
   datalines;
120102/Tom/Zhou/M/108,255/Sales Manager/01Jun1993
120103/Wilson/Dawes/M/87,975/Sales Manager/01Jan1978
120261/Harry/Highpoint/M/243,190/Chief Sales Officer/01Aug1991
121143/Louis/Favaron/M/95,090/Senior Sales Manager/01Jul2001
121144/Renee/Capachietti/F/83,505/Sales Manager/01Nov1995
121145/Dennis/Lansberry/M/84,260/Sales Manager/01Apr1980
;

title;
```

```
title 'Orion Star Management Team';
proc print data=work.managers noobs;
   format HireDate mmddyy10.;
run;
title;
```

### Validating data

When SAS encounters a data error, it prints messages and a ruler in the log and assigns a missing value to the affected variable. Then SAS continues processing.

---

***Missing values between delimiters (consecutive delimiters)***

```
INFILE 'raw-data-file-name' <DLM=> DSD;
```

The **DSD** option sets the default delimiter to a comma, treats consecutive delimiters as missing values and enables SAS to read values with embedded delimiters if the value is surrounded by quotation marks

---

***Missing values at the end of a line***

```
INFILE 'raw-data-file-name' MISSOVER;
```

With the **MISSOVER** option, if SAS reaches the end of a record without finding values for all fields, variables without values are set to *missing*.

## Manipulating Data 
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m421/m421_5_a_sum.htm)

### Using SAS functions

***SUM function***

```
SUM(argument1, argument2, ...)
```

- The arguments must be numeric values
- The **SUM** function ignores missing values, so if an argument has a missing value, the result of the SUM function is the sum of the nonmissing values
- If you add two values by **+**, if one of them is missing, the result will be a missing value which makes the **SUM** function a better choice

---

***DATE funtion***

```
YEAR(SAS-date)     
QTR(SAS-date)
MONTH(SAS-date)
DAY(SAS-date)
WEEKDAY(SAS-date)
TODAY()                /* Obtain the current date and convert to SAS-date (no argument) */
DATE()                 /* Obtain the current date and convert to SAS-date (no argument) */
MDY(month, day, year)
```

- The arguments must be numeric values (except from **TODAY()** and **DATE()** functions)
- You can subtract dates: **Agein2012=(Bday2012-Birth_Date)/365.25;**

---
***Concatenation function***

```
CATX(' ', First_Name, Last_Name)
```

The **CATX** function removes leading and trailing blanks, inserts delimiters, and returns a concatenated character string. In the code, you first specify a character string that is used as a delimiter between concatenated items.

---
***Time interval function***

```
INTCK('year', Hire_Date, '01JAN2012'd)
```

The **INTCK** function returns the number of interval boundaries of a given kind that lie between the two dates, times, or datetime values. In the code, you first specify the interval value.

---
***What happens if you use a variable to describe a new one that you are gonna DROP in that same DATA statement?***

The **DROP** statement is a compile-time-only statement. SAS sets a drop flag for the dropped variables, but the variables are in the PDV and, therefore, are available for processing.

### Conditional processing

***IF-THEN-ELSE conditional structures***

```
IF expression THEN statement;
ELSE IF expression THEN statement;
ELSE statement;
```

In the conditional expressions involving strings watch out for possible mixed case values where the condition may not be met:  **Country = upcase(Country);** to avoid problems

---

***Executing multiple statements in an IF-THEN-ELSE statement***

```
IF expression THEN
    DO;
        executable statements;
    END;
ELSE IF expression THEN
    DO;
        executable statements;
    END;
```
    
---
    
In the **DATA** step, the first reference to a variable determines its length. The first reference to a new variable can be in a **LENGTH** statement, an **assignment** statement, or **another** statement such as an INPUT statement. After a variable is created in the PDV, the length of the variable's first value doesn't matter. 

To avoid truncation in a variable defined inside a conditional structure you can:

- Define the longer string as the first condition
- Add some blanks at the end of shorter strings to fit the longer one
- Define the length explicitly before any other reference to the variable

---

***SELECT group***

```
SELECT(Gender);
      WHEN('F') DO;
         Gift1='Scarf';
         Gift2='Pedometer';
      END;
      WHEN('M') DO;
         Gift1='Gloves';
         Gift2='Money Clip';
      END;
      OTHERWISE DO;
         Gift1='Coffee';
         Gift2='Calendar';
      END;
END;
```

- The **SELECT** statement executes one of several statements or groups of statements
- The **SELECT** statement begins a SELECT group. They contain **WHEN** statements that identify SAS statements that are executed when a particular condition is true
- Use at least one **WHEN** statement in a SELECT group
- An optional **OTHERWISE** statement specifies a statement to be executed if no **WHEN** condition is met
- An **END** statement ends a **SELECT** group

## Combining SAS Data Sets
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m421/m421_5_a_sum.htm)

### Concatenating Data Sets

***Combine files vertically by concatenating***

```
DATA SAS-data-set;
    SET SAS-data-set1 SAS-data-set2 ...;
RUN;
```

***Combine two different variables that are actually the same one***

```
DATA SAS-data-set;
    SET SAS-data-set1 (RENAME=(old-name1 = new-name1 old-name2 = new-name2)) SAS-data-set2 ...;
RUN;
```

- The name change affects the PDV and the output data set, but has no effect on the input data set
- The **variable attributes** are assigned from the **first data set** in the SET statement
- You will get an **error** in the DATA step if a variable is defined with **different data types** in the files that you are trying to concatenate

### Merging SAS Data Sets One-to-One

***Combine files horizontally by merging***

- The **match-merging** is a process based on the values of common variables
- Data sets are merged in the order that they appear in the MERGE statement
- You may need to **SORT** the files by the **BY-variable(s)** before merging the files

```
DATA SAS-data-set;
    MERGE SAS-data-set1 (RENAME=(old-name1 = new-name1 ...)) SAS-data-set2 ...;
    BY <DESCENDING> BY-variable(s);
    <additional SAS statements>
RUN;
```

- In a **one-to-one** relationship, a single observastion in one data set is related to one, and only one, observation in another data set based on the values of one or more common variables
- In a **one-to-many** relationship, a single observation in one data set is related to one or more observations in another data set
- In a **many-to-one** relationship, multiple observations in one data set are related to one observation in another data set
- In a **many-to-many** relationship, multiple observations in one data set are related to multiple observations in another data set
- Sometimes the data sets have **non-matches**: at least one observation in one of the data sets is unrelated to any observation in another data set based on the values of one or more common variables

### Merging SAS Data Sets One-to-Many

```
DATA SAS-data-set;
    MERGE SAS-data-set1 SAS-data-set2 ...;
    BY <DESCENDING> BY-variable(s);
    <additional SAS statements>
RUN;
```

*In a **one-to-many merge**, does it matter which data set is listed first in the MERGE statement?*

When you reverse the order of the data sets in the MERGE statement, the results are the same, but the order of the variables is different. SAS performs a **many-to-one merge**.

---

**MERGENOBY** (= NOWARN (default) | WARN | ERROR) controls whether a message is issued when MERGE processing occurs without an associated BY statement

* Performing a merge without a BY statement merges the observations based on their positions
* This is almost never done intentionally and can lead to unexpected results

### Merging SAS Data Sets that Have Non-Matches

```
DATA SAS-data-set;
    MERGE SAS-data-set1 SAS-data-set2 ...;
    BY <DESCENDING> BY-variable(s);
    <additional SAS statements>
RUN;
```

* After the merging, the output data set contains **both matches and non-matches**
* You want the new data set to contain only the observations that match across the input data sets, and not those ones that are missing in one of the data sets that you are merging

```
DATA SAS-data-set;
    MERGE SAS-data-set1 (IN=variable1) 
          SAS-data-set2 (IN=variable2) ...;
    BY <DESCENDING> BY-variable(s);
    <additional SAS statements>
RUN;
```

* When you spefify the **IN** option after an input data set in the MERGE statement, SAS creates a **temporary numeric variable** that indicates whether the data set contributed data to the current observation (0 = it did not contribute to the current observation, 1 = it did contribute)
* These variables are only available **during execution**

```
DATA SAS-data-set;
    MERGE SAS-data-set1 (IN=variable1) 
          SAS-data-set2 (IN=variable2) ...;
    BY <DESCENDING> BY-variable(s);
    IF variable1 = 1 and variable2 = 1;     /* write only matches */
    <additional SAS statements>
RUN;`
```

- ***Matches***

```
IF variable1 = 1 and variable2 = 1 
IF variable1 and variable2
```

* ***Non-matches from either data set***

```
IF variable1 = 0 or not variable2 = 0
IF not variable1 or not variable2`
```

***E.g.:***<br>

```
DATA SAS-new-data-set1 SAS-new-data-set2;
	MERGE SAS-data-set1 (in=var1) SAS-data-set2 (in=var2);
	BY BY-variable(s);
	IF var2 THEN OUTPUT SAS-new-data-set1;
	ELSE IF var1 and not var2 THEN OUTPUT SAS-new-data-set2;
	KEEP variable1 variable2 variable5 variable8;
run;
```

## Creating Summary Reports
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECPRG193/m422/m422_5_a_sum.htm)

### Using PROC FREQ to Create Summary Reports

* When you're summarizing data, there's no need to show a frequency distribution for variables that have a large number of distinct values
* Frequency distributions work best with variables whose values meet two criteria: variable with **categorical values** and values are **best summarized by counts instead of averages**
* Variables that have continuous numerical values, such as dollar amounts and dates, will need to be **grouped into categories** by **applying formats** inside the PROC FREQ step (substitute an specific range of those values by a tag)

```
PROC FREQ DATA=SAS-data-set <option(s)>;
    TABLES variable(s) <loption(s)>;
    <additional statements>
RUN;
```

* **PROC FREQ** produces frequency tables that report the distribution of any or all variable values in a SAS data set
* In the **TABLE** statement you specify the frequency tables to produce 
* To create **one-way** frequency tables you specify one or more variable names separated by space
* **WATCH OUT**: if you omit the **TABLE** statement, SAS produces a one-way table for every variable in the data set
* The **PROC FREQ** step automatically displays output in a report, so you don't need to add a PROC PRINT step 
* Each unique variable's value displayed in the 1<sup>st</sup> column of the output is called a **level of the variable**

---

```
PROC FREQ DATA=SAS-data-set <option(s)>;
    TABLES variable/NOCUM NOPERCENT;
    <additional statements>
RUN;
```

* **NOCUM** option supresses the display of  the cummulative frequency and cummulative percent values 
* **NOPERCENT** option supresses the display of all percentages

---

```
PROC SORT DATA=SAS-data-set
    OUT=SAS-data-set-sorted;
    BY variable_sorted;
RUN;

PROC FREQ DATA=SAS-data-set-sorted;
    TABLES variable-freq;
    BY variable_sorted;
RUN;
```

- Whenever you use the **BY** statement, the data set must be sorted by the variable named in the statement
- Using this we will get a frequency table on **`variable_freq`** for each value of **`variable_sorted`**

---

***Crosstabulation tables***

* Sometimes it is useful to view a single table with statistics for each distintic combination of values of the selected variables
* The simplest crosstabulation table is a **two-way table**

```
PROC FREQ DATA=SAS-data-set;
    TABLES variable1 * variable2 / NOFREQ NOPERCENT NOROW NOCOL;
RUN;

variable1 = table rows
variable2 = table columns
```

Information contained in crosstabulation tables (legend):

* **Frequency**: indicates the number of observations with the unique combination of values represented in that cell
* **Percent**: indicates the cell's percentage of the total frequency
* **Row Pct**: cell's percentage of the total frequency for its row
* **Col Pct**: cell's percentage of the total frequency for its column 
<br><br>
* **LIST** option format: the first two columns specify each possible combination of the two variables; it displays the same statistics as the default **one-way frequency** table
* **CROSSLIST** option format: it displays the same statistics as the default **crosstabulation** table

--- 

The **FORMAT=** option allows you to format the frequency value (to any SAS numeric format or a user-defined numeric format while its length is not more than 24) and to change the width of the column (e.g. to allow variable labels to fit in one line). 

```
PROC FREQ DATA=SAS-data-set;
    TABLES variable1 * variable2 /
    FORMAT = <w>.;
    FORMAT variable1 $format-name.;    
RUN;
```

The **FORMAT=** option applies only to crosstabulation tables displayed in the default format. It doesn't apply to crosstabulation tables produced with the **LIST**/**CROSSLIST** option

### Using PROC FREQ for Data Validation

You can use a **PROC FREQ** step with the **TABLES** statement to detect invalud numeric and character data by looking at distinct values. The **FREQ** procedure **lists all discrete values** for a variable and **reports its missing values**.

```
PROC FREQ DATA=SAS-data-set <ORDER=FREQ>;
    TABLES variable;
RUN;
```

* You can check for non-expected variable's values
* You can check for missing values
* You can find duplicated values

---

The table showing the **Number of Variable Levels** can indicate whether a variable contains duplicate/missing/non-expected values:

```
PROC FREQ DATA=SAS-data-set NLEVELS;
    TABLES variable / NOPRINT;
RUN;
```

---

You can use a **WHERE** statement to print out only the invalid values to be checked:

```
PROC PRINT DATA=SAS-data-set;
    WHERE gender NOT IN ('F','M') OR
          job_title IS NULL OR
          salary NOT BETWEEN 24000 AND 500000 OR
          employee IS MISSING;
RUN;
```

---

You can output the tables to a new data set instead of displaying it:

```
PROC FREQ DATA=SAS-data-set NOPRINT;
   TABLE variable / OUT=SAS-new-data-set;
RUN;
```

### Using the MEANS and UNIVARIATE Procedures

**PROC MEANS** produces summary reports with descriptive statistics and you can create statistics for groups of observations

* It automatically displays output in a report and you can also save the output in a SAS data set
* It reports the **number of nonmissing values** of the analysis variable (N), and the **mean**, the **standard deviation** and **minimum/maximum values** of every numeric variable in the data set
* The variables in the **CLASS** statement are called **classification variables** or **class variables** (they typically have few discrete values)
* Each combination of class variable values is called a **class level**
* The data set **doesn't need to be sorted** or indexed by the class variables
* **N Obs** reports the number of observations with each unique combination of class variables, whether or not there are missing values (if these **N Obs** are identical to **N**, there are no missing values in you data set)

```
PROC MEANS DATA=SAS-data-set <statistic(s)>;
    VAR analysis-variable(s);
    CLASS classification-variable(s);
RUN;
```

To write the report in a new data set (including total addition):

```
PROC MEANS DATA=SAS-data-set NOPRINT NWAY;
	OUTPUT OUT=SAS-new-data-set SUM=addition-new-variable;
    VAR analysis-variable(s);
    CLASS classification-variable(s);
RUN;
```

Format options: 

* **MAXDEC=number** (default format = BESTw.) 
* **NONOBS**
* **FW=number**: specifies that the field width for all columns is *number*
* **PRINTALLTYPES**: displays statistics for all requested combination of class variables

![alt text](descriptive_statistics.png![enter image description here](https://lh3.googleusercontent.com/R84N_PMRcXBBgDksyuhN6i--5J_vun1oLe5CRgMIvZdFZNSbSAxMkrKzCo5z7Zn_2aPnoFY=s0 "Descriptive statistics")
![alt text](quantile_statistics.png![enter image description here](https://lh3.googleusercontent.com/aQuAOJzy4JgnaWUPOUwU80TvOp9DeQXr3Iesbw1EVHVJrZKjUw-TC4S27Mhd6Dt8NJ7V7j4=s0 "Quantile statistics")

---

***Alternative procedure to validate data: *** **PROC MEANS**

* The **MIN**/**MAX** values can be useful to check if the data is within a range
* **NMISS** option displays the number of observations with missing values

---

***Alternative procedure to validate data: *** **PROC UNIVARIATE**

**PROC UNIVARIATE** is a procedure that is useful for detecting data outliers that also produces summary reports of **descriptive statistics**

```
PROC UNIVARIATE DATA=SAS-data-set;
    VAR variable(s);
    ID variable_to_relate;
    HISTOGRAM variables </options>;
    PROBPLOT variables </options>;
    INSET keywords </options>;
RUN;
```

* If you omit the **VAR** statement, all numeric variables in the data set are analyzed
* The **Extreme Observations** table contains useful information to locate outliers: it displays the 5 lowest/highest values by default along with the corresponding observation number. The **ID** statement specifies that SAS will use this variable as a label in the table of extreme observations and as an identifier for any extreme.
* To specify the number of listed observations you can use **NEXTROBS=**
* **HISTOGRAM/PROBPLOT** options: normal(mu=est sigma=est) creates a normal curve overlay to the histogram using the estimates of the population mean and standard deviation
* **INSET** writes a legend for the graph. `/ position=ne` moves the **INSET** to the north-east corner of the graph.

To include in the report only one of the automatically produced tables:

1) Check the specific table name in the **LOG information** using **ODS TRACE**:

```
ODS TRACE ON;
PROC UNIVARIATE DATA=SAS-data-set;
    VAR variable(s);
RUN;
ODS TRACE OFF;
```

2) Select the wanted table with **ODS SELECT**:

```
ODS SELECT ExtremeObs;
PROC UNIVARIATE DATA=SAS-data-set;
    VAR variable(s);
RUN;
```

---

***SUMMARY of validation procedures***

![alt text](validation_procedures.png![enter image description here](https://lh3.googleusercontent.com/qa02E3GQU_EU1ZHWX40Ewy-WsXd7hmzfJ5HXBOCDvHrtxRGjrlh6R3hjEupj5Ul9mDreXO8=s0 "Validation procedures")

### Using the SAS Output Delivery System

```
ODS destination FILE="filename" <options>;
    <SAS code to generate the report>
ODS destination CLOSE;`
```

* You can have multiple destinations open and execute multiple procedures
* All generated output will be sent to every open destination
* You might not be able to view the file, or the most updated file, outside of SAS until you close the destination

**E.g.:**

```
ODS pdf FILE="C:/output/test.pdf";
(...)
ODS pdf CLOSE;`

ODS csvall FILE="C:/output/test.cvs";
ODS rtf FILE="C:/output/test.rtf";
(...)
ODS csvall CLOSE;
ODS rtf CLOSE;
```

***Allowed file formats and their corresponding destinations:***
![alt text](SAS_output_delivery_system.png![enter image description here](https://lh3.googleusercontent.com/p3gAmRNbwqP8WfUOSKCLxTA042D3e_F9OUkxYJ0XHspC7MAfzfAnK0ghvpLZQXJWNWdbPd0=s0 "SAS Output Delivery System")

# Statistics: Introduction to ANOVA, Regression and Logistic Regression

## Introduction to Statistics
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m551/m551_6_a_sum.htm)

### Basic Statistical Concepts

* ***Descriptive statistics (exploratory data analysis, EDA)***
  * Explore your data
* ***Inferential statistics (explanatory modelling)***
 * **How is X related to Y?**
 * Sample sizes are typically small and include few variables
 * The focus is on the parameters of the model
 * To assess the model, you use p-values and confidence intervals
* ***Predictive modelling***
 * **If you know X, can you predict Y?**
 * Sample sizes are large and include many predictive (input) variables
 * The focus is on the predictions of observations rather than the parameters of the model
 * To assess a predictive model, you validate predictions using holdout sample data
 
---
 
**How to generate random (representative) samples (population subsets)**

```
PROC SURVEYSELECT DATA=SAS-data-set 
                  OUT=name-of-output-data-set
                  METHOD=method-of-random-sampling
                  SEED=seed-value 
                  SAMPSIZE=number-of-observations-desired;
     <STRATA stratification-variable(s);>
RUN;
```

* **METHOD**: specifies the random sampling method to be used. For simple random sampling without replacement, use **METHOD=SRS**. For simple random sampling with replacement, use **METHOD=URS**. For other selection methods and details on sampling algorithms, see the SAS online documentation for PROC SURVEYSELECT.
* **SEED**: specifies the initial seed for random number generation. If no SEED option is specified, SAS uses the system time as its seed value. This creates a different random sample every time the procedure is run.
* **SAMPSIZE**: indicates the number of observations to be included in the sample. To select a certain fraction of the original data set rather than a given number of observations, use the **SAMPRATE** option.

---

* **Parameters**: numerical values (typically unknown, you can't measure the entire population) that summarize characteristics of a population (greek letters)
* **Statistics**: summarizes characteristics of a sample (standard alphabet)
* You use **statistics** to estimate **parameters**

---

* **Independent variable**: it can take different values, it affects or determines a **dependent variable**. It can be called predictor, explanatory, control or input variable.
* **Dependent variable**: it can take different values in response to an **independent variable**. Also known as response, outcome or target variable.

---

***Scale of measurement***: variable's classification
<br>

* **Quantitative/numerical variables**: counts or measurements, you can perform arithmetical operations with it
 * **Discrete data**: variables that can have only a countable number of values within a measurement range
 * **Continuous data**: variables that are measured on a scale that has infinite number of values and has no breaks or jumps
   * **Interval scale data**: it can be rank-ordered like ordinal data but also has a sensible spacing of observations such that differenes between measurements are meaningful but it lacks a true zero (ratios are meaningless)
   * **Ratio scale data**: it is rank-ordered with meaningful spacing and also includes a true zero point and can therefore accurately indicate the ratio difference between two spaces on the measurement scale
* **Categorical/attribute variables**: variables that denote groupings or labels
 * **Nominal data (qualitative/classification variable)**: exhibits no ordering within its observed levels, groups or categories
 * **Ordinal data**: the observed labels can be ordered in some meaningful way that implies that the differences between the groups or categories are due to magnitude

---

* **Univariate analysis** provides techniques for analyzing and describing a sigle variable. It reveals patterns in the data by looking at the **range** of values, measures of **dispersion**, the **central tendecy** of the values and **frequency distribution**.
* **Bivariate analysis** describes and explains the relationships between two variables and how they change or covary together. It include techniques such as **correlation analysis** and **chi-square tests of independance**.
* **Multivariate/Multivariable analysis** examines two or more variables at the same time in order to understand the relationships among them. 
 * Techniques such as **mutiple linear regression** and n-way **ANOVA** are typically called **multivariable** analysis (only one response variable). 
 * Techniques such as **factora analysis** and **clustering** are typically called **mutivariate** analysis (they consider more than one response variable).


### Descriptive Statistics

**Measures of central tendencies**: mean (affected by outliers), median (less sensitive to outliers), mode

25th percentile = 1st/lower quartile = Q1<br>
50th percentile = median = middle quartile = Q2<br>
75th percentile = 3rd/upper quartile = Q3<br>

The **interquartile range (IQR)** is the difference between Q1 and Q3, it is a **robust estimate of the variability** because changes in the upper/lower 25% of the data do not affect it. If there are **outliers** in the data, then the IQR is a more reliable measure of the spread than the overall range.

The **coefficient of variation (CV)** is a measure of the standard deviation expressed as a percentage of the mean (S/mean\*100)

### Picturing Your Data

**Normal distribution**: (&mu;-&sigma;,&mu;+&sigma;) = 68%; (&mu;-2&sigma;,&mu;+2&sigma;) = 95%; (&mu;-3&sigma;,&mu;+3&sigma;) = 99%

*How to check the normality of a sample?*

* Compare the **mean** and the **median**: if they are nearly equal, that is an indicator of symmetry (requirement for normality).
* Check that **skewness** and **kurtosis** are close to 0.

***Statistical summaries:*** **skewness** and **kurtosis** measure certain aspects of the shape of a distribution (they are **0** and **3** for a normal distribution, although SAS has standardized both to 0)

* **Skewness** measures the tendency of your data to be more spread out on one side of the mean than on the other (asymmetry of the distribution). 
 * You can think of the direction of skewness as the direction the data is trailing off to. 
 * A **right-skewed** distribution tells us that the mean is **greater than the median**.
* **Kurtosis** measures the tendency of your data to be concentrated toward the center or toward the tails of the distribution (peakedness of the data, tail thickness). 
 * A **negative kurtosis (platykurtic distribution)** means that the data has lighter tails than in a normal distribution. 
 * A **positive kurtosis (leptokurtic/heavy-tailed/outlier-prone distribution)** means that the data has heavier tails and is more concentrated around the mean than a normal distribution.
 * Rectangular, bimodal and multimodal distributions tend to have low values of kurtosis.
 * **Asymmetric distributions** also tend to have nonzero kurtosis. In these cases, understanding kurtosis is considerably more complex and can be difficult to assess visually.
* If **skewness/kurtosis**:
 * Both are greater than 1 or less than -1: data is not normal
 * Either is greater than 2 or less than -2: data is not normal

---

***PLOTS PRODUCED WITH PROC UNIVARIATE***

* **Histograms**
* **Normal probability plots**: expected percentiles from standard normal vs actual data values

![alt text](normalprobplots.png![enter image description here](https://lh3.googleusercontent.com/oQg9v6o7-BVphCe0xL8cP2L49JBQL7hixl7_uwJUEKQkMdbotX-f906RXjowuwCe3llq05I=s0 "Normal Probability Plots")

***PLOTS PRODUCED WITH PROC SGSCATTER***

* **Scatter plots**: you can create a **single-cell** (simple Y by X) scatter plot, a **multi-cell** scatter plot with multiple independent scatter plots in a grid and a **scatter plot matrix**, which produces a matrix of scatter plots comparing multiple variables.

***PLOTS PRODUCED WITH PROC SGPLOT***

```
PROC SGPLOT DATA=SAS-data-set <options>;
        DOT category-variable </options>;
        HBAR category-variable </options>;
        VBAR category-variable </options>;
        HBOX response-variable </options>;
        VBOX response-variable </options>;
        HISTOGRAM response-variable </options>;
        SCATTER X=variable Y=variable </options>;
        NEEDLE X=variable Y=numeric-variable </options>;
        REG X=numeric-variable Y=numeric-variable </options>;
RUN;
```

Anywhere in the procedure you can add **reference lines**:<br>
```
REFLINE variable | value-1 <... value-n> </option(s)>`<br>
**E.g.:** `REFLINE 1200 / axis=y lineattrs=(color=blue);`</option(s)>
```
**E.g.:** 
```
REFLINE 1200 / axis=y lineattrs=(color=blue);
```

* **Scatter plots (SCATTER)**
* **Line graphs**
* **Histograms (HISTOGRAM)** with overlaid distribution curves
* **Regression lines (REG)** with confidence and prediction bands
* **Dot plots (DOT)**
* **Box plots (HBOX/VBOX)**: it makes it easy to see how spread out your data is and if there are any outliers. The box represents the middle 50% of your data (IQR). The lower/middle/upper **line of the box** represent Q1/Q2/Q3. The **diamond** denotes the mean (easy to check how close the mean is to the median). The **whiskers** extend as far as the data extends to a maximum length of 1.5 times the IQR above Q3. Any data points farther than this distance are considered possible outliers and are represented in this plot as **circles**.
* **Bar charts (HBAR/VBAR)**
* **Needle plot (NEEDLE)**: creates a plot with needles connecting each point to the baseline
* You can also **overlay plots together** to produce many different types of graphs

***PLOTS PRODUCED WITH PROC SGPANEL***

* **Panels of plots** for different levels of a factor or several different time periods depending on the classification variable
* **Side-by-side histograms** which provide a visual comparison for your data

***PLOTS PRODUCED WITH PROC SGRENDER***

* **Plots from graphs templates you have modified or written yourself**

---

To specify options for graphs you submit the **ODS GRAPHICS** statement:

```
ODS GRAPHICS ON <options>;
```

* To select/exclude specific test results, graphs or tables from you output, you can use **ODS SELECT** and **ODS EXCLUDE** statements.
* You can use ODS templates to modify the layout and details of each graph
* You can use ODS styles to control the general appearance and consistency of yous graphs and tables (by default **HTMLBLUE**).

Another way to control you output is to use the **PLOT** option which is usually available in the procedure statement:<br>
`PROC UNIVARIATE DATA=SAS-data-set PLOTS=options;`<br>
This option enables you to specify which graphs SAS should create, either in addtion or instead of the default plots.

### Confidence Intervals for the Mean

* A **point estimator** is a sample statistic used to estimate a population parameter
* An estimator takes on different values from sample to sample, so it's important to know its variance
* A statistic that measures the variability of your estimator is the **standard error**
* It differs from the standard deviation: the **standard deviation** deals with the variability of your data while **standard error** deals with the variability of you sample statistic

**E.g.:** Standard error of the mean = standard deviation/sqrt(sample size)

The **distribution of sample means** is always less variable than the data.

* Because we know that point estimators vary from sample to sample, it would be nice to have an estimator of the mean that directly accounts for this natural variability
* The **interval estimator** gives us a range of values that is likely to contain the population mean
* It is calculated from the **standard error** and a value that is determined by the **degree of certainty** we require (**significance level**)
* **Confidence intervals** are a type of interval estimator used to estimate the population mean
* You can make the confidence interval narrower by increasing the sample size and by decreasing the confidence level

CI = sample mean &plusmn; quantile * standard error

* The **CLM** option of **PROC MEANS** calculates the confidence limits for the mean, you can add **alpha=** to change the default 0.05 value for a 95% confidence level
* The **central limit theorem** states that the distribution of sample means is approximately normal regardless of the population distribution's shape, if the sample size is large enough (~30 observations)

### Hypothesis Testing

* The **null hypothesis (H0)** is what you assume to be true when you start your analysis
* The **alternative hypothesis (Ha/H1)** is your initial research hypothesis, that is, your proposed explanation

Decision-making process:
1. Define null and alternative hypothesis
2. Specify significance level (type I error rate)
3. Collect data
4. Reject or fail to reject the null hypothesis

![alt text](errortype.PNG![enter image description here](https://lh3.googleusercontent.com/KaQmpAoTHu1NsLpiBusArHKbs5Zn0AP5eV0CB2PwBObxixZQ98gaUDJVGZSnSj8Li4Hwfvw=s0 "Error types")

* The type I and II errors are **inversely related**: as one type increases the other decreases 
* The **power** is the probability of a **correct rejection** = 1 - &beta;
 * It is the ability of the statistical test to detect a true difference
 * It is the ability to successfully reject a false null hypothesis
 

* A **p-value** measures the probability of observing a value as extreme as the one observed
 * The p-value is used to determine **statistical significance**
 * It helps you assess whether you should reject the null hypothesis
* The **p-value** is affected by:
 * The **effect size**: the difference between the observed statistic and the hypothesized value
 * The **sample size**: the larger the sample size, the more sure you are about the sample statistics, the lower the p-value is
 
 
* A reference distribution enables you to quantify the probability (p-value) of observing a particular outcome (the calculated sample statistic) or a more extreme outcome, if the nul hypothesis is true
* Two common reference distributions for statistical hypothesis testing are the **t distribution** and the **F distribution**
* These distributions are characterized by the **degrees of freedom** associated with your data
* The **t distribution** arises when you're making inferences about a population mean and the population standard deviation is unknown and has to be estimated from the data
 * It is **approximately normal** as the **sample size grows larger**
 * The t distribution is a **symmetric distribution** like the normal distribution except that the t distribution has **thicker tails**
 * The **t statistic** is positive/negative when the sample is more/less than the hypothesized mean
 * If the data doesn't come from a normal distribution, then the t statistic approximately follows a t distribution as long as the sample size is large (**central limit theorem**)
 
Calculation with **PROC UNIVARIATE**:

```
ODS SELECT TESTSFORLOCATION;
PROC UNIVARIATE DATA=SAS-data-set MU0=number alpha=number;
    VAR variable(s);
    ID variable_to_relate;
    HISTOGRAM variables </options>;
    PROBPLOT variables </options>;
    INSET keywords </options>;
RUN;
```

* **TESTSFORLOCATION** displays only the p-values calculation
* By default **MU0 = 0**

## Analysis of Variance (ANOVA)

[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m552/m552_7_a_sum.htm)

![enter image description here](https://lh3.googleusercontent.com/xOC5eoOUs-6v-b-VnQU6ivGQQPIOQH7ACcKMS2jfrOTK1HJLuBbchpYm3cuganuJ_gNJsBU=s0 "ANOVA")

### Graphical Analysis of Associations

* Before analyzing your data, you need to have a general idea of any associations between **predictor variables** and **response variables**
* An **association** exists between two variables when the expected value of one variable differs at different levels of the other variable
* One method for doing this is to conduct a **graphical analysis** of your data
* Associations between **categorical** predictor variable and a **continuous** response variable can be explored with **SGPLOT** to product **box plots (box-and-whisker plots)** (**X** predictor variable vs **Y** response variable)
* If the **regression line** conecting the means of Y at each value of X is not horizontal **there might be an association** between them
* If the **regression line** is horizontal **there is no association**: knowing the value of X doesn't tell you anything about the value of Y

```
PROC SGPLOT DATA=SAS-data-set;
		VBOX response-variable / CATEGORY=predictor-variable
		CONNECT=MEAN DATALABEL=outlier-ID-variable;
RUN;
```

### Two-Sample t-Tests

* You can use a **one-sample t-test** to determine if the mean of a population is equal to a particular value or not
* When you collect a random sample of independent observations from two different populations, you can perform a **two-sample t-test**

When you compare the means of two populations using a **two-sample t-test** you make three assumptions:

* The data contains independent observations
* The distributions of the two populations are normal (check histograms and normal probability/Q-Q plots)
* The variances in these normal distributions are equal (**F-test** is the formal way to verify this assumption)
F statistic: $F=max(s_1^2,s_2^2)/min(s_1^2,s_2^2) \ge 1$
H0: &sigma;$_1^2$ $=$  &sigma;$_2^2\rightarrow F \approx 1$
Ha: &sigma;$_1^2$ $\ne$  &sigma;$_2^2\rightarrow F\gt 1$
The **Pr>F** value in the **Equality of Variances** table represents the **p-value** of the F-test for equal variances

**Two-sided Tests**

* **PROC TTEST** performs a two-sided two-sample t-test by default (confidence limits and ODS graphics included)
* It **automatically test the assumption of equal variances** and provides an exact two-sample t-test (**pooled**) when the assumptions are met and an approximate t-test (**scatterthwaite**) when it is not met 
* The pooled and scatterthwaite t-tests are equal when the variances are equal

```
PROC TTEST DATA=SAS-data-set <options>;
                         plots(shownull)=interval;         \* shownull = vertical reference line at the mean value of H0 *\
	CLASS variable;                                              \* Classification variable *\
	VAR variable(s);                                              \* Continuous response variables *\
RUN;
```

**One-sided Tests**

* It **can increase the power** of a statistical test, meaning that if you are right about the direction of the true difference, you will more likely detect a significant difference with a one-sided test than with a tow-sided test
* The difference between the mean values for the null hypothesis will be defined by the alphabetical order of the classification variables (e.g.: female - male)

```
PROC TTEST DATA=SAS-data-set 
                       plots(only shownull)=interval H0=0 SIDES=u;         \* only = suppress the default plots; u/l = upper/lower-tailed t-test  *\
	CLASS variable;                                              \* Classification variable *\
	VAR variable(s);                                              \* Continuous response variables *\
RUN;
```

### One-Way ANOVA

When you want to determine whether there are significant differences between the **means of two or more populations**, you can use analysis of variance (ANOVA).

* You have a continuous dependent (**response**) variable and a categorical independent (**predictor**) variable
* You can have **many levels of the predictor variable**, but you can have **only one predictor variable**
* The **squared value of the t statistic** for a two-sample t-test is equal to the **F statistic** of a one-way ANOVA with two populations
* With ANOVA the **H0** is that all of the population means are equal and **Ha** is that not all the population means are equal (at least one mean is different)

To perform an ANOVA test you make three assumptions:

* You have a **good, random, representative sample**
* The **error terms are normally distributed**
 * The **residuals** (each observation minus its group mean) are estimates of the error term in the model so you verify this assumption by examining diagnostic plots of the residuals (if they are approximately normal, the error terms will be too)
 * If your sample sizes are reasonably large and approximately equal across groups, then only severe departures from normality are considered a problem
 * Residuals always sum to 0, regardless of the number of observations.
* The **error terms have equal variances** across the predictor variable levels: you can conduct a formal test for equal variances and also plot the residuals vs predicted values as a way to graphically verify this assumption

You can use **PROC GLM** to verify the ANOVA assumptions and perform the ANOVA test. It fits a general linear model of which ANOVA is a special case and also displays the sums of squares associated with each hypothesis it tests.
```
PROC GLM DATA=SAS-data-set
                  PLOTS(ONLY)=DIAGNOSTICS(UNPACK);   /* print each plot on a separated page */
	CLASS variable(s);
	MODEL dependents=intependents </options>;
	MEANS effects / HOVTEST </options>;                /* HOVTEST = homogeneity of variance test option (Levene's test by default) + plot of residuals vs predicted values (means) */
RUN;
QUIT;
```

---

* Of the **between-group variability** is significantly larger than the **within-group variability**, you reject the null that all the group means are equal
* You partition out the variability using sums of squares: 
 * **Between-group** variation: also called Model Sum of Squares (SSM): $\sum n_i (\overline Y_i- \overline {\overline Y})^2$
 * **Within-group** variation: also called Error Sum of Squares (SSE): $\sum \sum (Y_{ij}- \overline Y_i)^2$
 * **Total** variation: also called the Total Sum of Squares (SST): $\sum \sum (Y_{ij}- \overline {\overline Y})^2$
* **SSM** and **SSE** represent pieces of **SST**: the SSM is the variability explanied by the predictor variable levels and SSE the variability not explained by the predictor variable levels
* You want the larger piece of the total to be better represented by what you can explain (SSM) vs what you cant't explain (SSE) 

### ANOVA with Data from a Randomized Block Design

In an **observational study**, you often examine what already occurred, and therefore have little control over factors contributing to the outcome. In a **controlled experiment**, you can manipulate the **factors of interest** and can more reasonably claim causation.

* The variation due to the **nuisance factors** (fundamental to the probabilistic model but are no longer of interest) is part of the random variation that the error sum of squares accounts for.
* Including a **blocking variable** in the model is in essence like adding a second predictor variable to the model in terms of the way you write it
* The way you set up your experiment and data collection is what defines it as a blocking factor
* Although you're not specifically interested in its effect, **controlling the blocking variable makes it easier to detect an effect of the factor of interest**
* In a model that does not include a blocking variable, its effects are lumped into the error term of the model (unaccounted for variation)
* When you include a blocking variable in your ANOVA model, any effects caused by the nuisance factors that are common within a sector are accounted for in the **model sum of squares rather than the error sum of squares**

You make two more assumptions when you include a blocking factor in the model:

* Primary variable levels are **randomly assigned** within each block
* The effects of the primary variable are **constant across the levels** of the blocking factor (the effects don't depend on the block they are in, there are **no interactions** with the blocking variable)

**Note:** Levene's test for homogeneity is **only available for one-way ANOVA models**, so in this case, you have to use the Residuals by Predicted plot.

```
PROC GLM DATA=SAS-data-set
                 PLOTS(ONLY)=DIAGNOSTICS(UNPACK);   /* print each plot on a separated page */
	CLASS variable(s) blocking-factor(s);
	MODEL dependents=intependents blocking-factor(s)</options>;
RUN;
QUIT;
```

* ***Rule of thumb***: if the **F-value is > 1**, then it helped to add the blocking factor in your model 
* If you compare the MSE (*Mean Square* in the table) without and with including the blocking variable in the model,  there is a drop of its value meaning that **you have been able to account for a bit more of the unexplained variability due to the nuisance factors** helping o have more precise estimates of the effect of your primary variable
* It is also reflected in the *R-Square* value that is increased when a blocking factor is added to the model
* Thanks to adding a blocking variable to your model you can get your primary variable to be significant
* The **Type III SS** at the bottom of the output tests for the difference due to each variable, controlling for or adjusting for the other variable

### ANOVA Post Hoc Tests

This test is used to determine which means differ from other means and control the error rate using **multiple comparison method**.

Assuming the null hypothesis is true for your different comparisons, the probability that you conclude a difference exist at least one time when there really  isn't a difference increases with the more tests you perform. So **the chance that you make a Type I error increases each time you conduct a statistical test**.

* The **comparisonwise error rate (CER)** is the probability of a Type I error on a single pairwise test (&alpha;)
* The **experimentwise error rate (EER)** is the probability of making at least one Type I error when performing the whole set of comparisons. It takes into consideration the number of pairwise comparisons you make, so it increases as the number of tests increase: $EER=1-(1-\alpha)^{\# \\\ of \\\ comparisons}$

***Tukey's Multiple Comparison Method***

* This method, which is also known as the **Honestly Significant Difference** test, is a popular multiple comparison test that **controls the EER**
* This tests compares all possible pairs of means, so **it can only be used when you make pairwise comparisons**
* This method controls $EER=\alpha$ when **all possible pairwise comparisons are considered** and controls $EER<\alpha$ when fewer than all pairwise comparisons are considered

***Dunnett's Multiple Comparison Method***

* This method is a specialised multiple comparison test that allows you to **compare a single control group to all other groups**
* It controls $EER \le \alpha$ when all groups are compared to the reference group (control)
* It accounts for the correlation that exists between the comparisons and **you can conduct one-sided tests** of hypothesis against the reference group

```
PROC GLM DATA=SAS-data-set;
	CLASS variable(s);
	MODEL dependents=intependents </options>;
	LSMEANS effects </options-test-1>;  
	LSMEANS effects </options-test-2>;
	[...]
	LSMEANS effects </options-test-n>;  
RUN;
QUIT;
```

* **PDIFF=ALL** requests p-values for the differences between ALL the means and a **diffogram** is produced automatically displaying all pairwise least square means differences and indicating which are significant
 * It can be undestood as a least squares mean by least squares mean plot
 *  The point estimates for differences between the means for each pairwise comparison can be found at the intersections of the gray grid lines (intersection of appropriate indexes)
 * The red/blue diagonal lines show the **confidence intervals for the true differences of the means** for each pairwise comparison
 * The grey 45$^{\circ}$ reference line represents equality of the means (if the confidence interval crosses over it, then there is no significant difference between the two groups and the diagonal line for the pair will be **dashed and red**; if the difference is significant the line will be **solid and blue**)

![enter image description here](https://lh3.googleusercontent.com/-yuw0XR4JPqs/WNknWl3atwI/AAAAAAAAABk/V_lTXMtgO_QDm7VJ9jPy29h7MIxZbyhzQCLcB/s0/diffogram.png "Diffogram")

* The **ADJUST=** option specifies the adjustment method for multiple comparisons
* If you don't specify an option SAS uses the **Tukey method by default**, if you specify **ADJUST=Dunnett** the GLM procedure produces multiple comparisons using **Dunnett's method** and a **control plot** 
 * The control plot displays the least squares mean and confidence limits of each group compared to the reference group 
 * The middle **horizontal line represents its least square mean value** (you can see the arithmetic mean value un the **upper right corner** of the graph)
 * The **shaded area** goes from the **lower decision limit (LDL)** to the **upper decision limit (UDL)**
 * There is a vertical line for each group that you're comparing to the reference (control) group. If a **vertical line extends past the shaded area**, then the group represented by the line is **significantly different** (small p-value) than the reference group 

![enter image description here](https://lh3.googleusercontent.com/-ZI5PKbFT1ns/WNkoofa4E3I/AAAAAAAAAB0/0RNlG7_94QMV3s864uB5UncYOw7VEMkYgCLcB/s0/controlplot.PNG "Control plot")

* **PDIFF=CONTROLU('value')** specifies the control group for the Dunnett's case: the direction of the sign in Ha is the same as the direction you are testing, so this is a **one-sided upper-tailed t-test**
* If you specify **ADJUST=T** SAS will make no adjustments for multiple comparisons: is not recommended as there's a tendency to find **more significant pairwise differences than might actually exist**

### Two-Way ANOVA with Interactions

When you have a continuous response variable and **two categorical predictor variables**, you use the **two-way ANOVA model**

* **Effect**: the magnitude of the expected change in the response variable presumably caused by the change in value of a predictor variable in the model
* In addition, the variables in a model can be referred to as effects or terms
* **Main effect**: is the effect of a single predictor variable
*  **Interaction effects**: when the relationship of the response variable with a predictor changes with the changing of another predictor variable (the effect of one variable depends on the value of the other variable)

![enter image description here](https://lh3.googleusercontent.com/-GK8G9YC7d1s/WNk5aOIcxAI/AAAAAAAAACM/nuq7AoAjh98_cci-KnaWTzhjbsCW_mSHACLcB/s0/interactionplot.png "Interaction plot")

When you consider an ANOVA with more than one predictor variable, it's called **n-way ANOVA** where *n* represents the number of predictor variables

* The analysis in a **randomized block design** is actually a **special type of two-way ANOVA** in which you have one factor of interest and one blocking factor
* When you analyze a two-way ANOVA with interactions, you first look at any tests for **interactions among the factors**
 * If there is **no interaction between the factors** you can interpret the tests for the individual factor effects to determine their significance/non-significance
 * If an **interaction exists between any factors**, the tests for the individual factor effects might be misleading due to masking of these effect by the interaction (this is specially true for unbalanced data with different number of observations for each combination of groups)
* When the interaction is not statistically significant **you can analyze the main effect with the model in its current form** (generally the method you use when you analyze designed experiments)
* Even when you analyze designed experiments, some statisticians might suggest that if the interaction is not significant, **you can delete the interaction effect from your model, rerun the model and then just analyze the main effects** increasing the power of the main effects test
* If the **interaction term is significant**, it is good practice to keep the main effect terms that make up the interaction in the model, whether they are significant or not (this preserves model hierarchy)
* You have to make the **same three assumptions used in the ANOVA test**
* The interaction terms are also called **product terms** or **crossed effects**

```
PROC GLM DATA=SAS-data-set;
	CLASS independent1 independent2;
	MODEL dependent = independent1 independent2 independent1*independent2;
	or
	MODEL dependent = independent1 | independent2;
RUN;
QUIT;
```

This program is **fitting to this model**:
$Y_{ijk}=\mu + \alpha_i+\beta_j+(\alpha\beta)_{ij}+\epsilon_{ijk}$
dependent = overall mean + intependent1 + independent2 + interaction12 + unaccounted for variation 

* In **most situations** you will want to use the **Type III SS**
* The **Type I SS (sequential)** are the sums of squares you obtain from fitting the effects in the order you specify in the model 
* The **Type III SS (marginal)** are the sums of squares you obtain from fitting each effect after all the other terms in the model, that is the sums of squares for each effect corrected for the other terms in the model
* When examining these results you first have to **look at the interaction term and if it's significant** (p-value), the **main effects don't tell you the whole story**. It that is the case, you don't need to worry all that much about the significance of the main effects at this point for two reasons:
 * You know that the effect of each variable1 level changes for the different variable2 levels
 * You want to include the main effects in the model, whether they are significant or not, to preserve model hierarchy
* You can analyze the interaction between terms by looking at the **interaction plot** that SAS produces by default when you include an interaction term in the model
* To analyze and interpret the effect of one of the interacting variables you need to add the **LSMEANS** statement to your program

```
PROC GLM DATA=SAS-data-set ORDER=INTERNAL PLOTS(ONLY)=INTPLOT;
	CLASS independent1 independent2;
	MODEL dependent = intependent1 independent2 independent1*independent2;
	LSMEANS independent1*independent2 / SLICE= independent1;
RUN;
QUIT;
```

SAS creates two types of mean plots when you use the LSMEANS statement with an interaction term:

* The first plot displays the **least squares mean (LS-Mean) for every effect level** 
* The second plot contains the same information rearranged so you can **look a little closer at the combination levels**

---

You can add a **STORE** statement to save your analysis results in an **item store** (a binary file format that cannot be modified). This allows you to **run post-processing analysis** on the stored results even if you no longer have access to the original data set. The STORE statement applies to the following SAS/STAT procedures: GENMOD, GLIMMIX, GLM, GLMSELECT, LOGISTIC, MIXED, ORTHOREG, PHREG, PROBIT, SURVEYLOGISTIC, SURVEYPHREG, and SURVEYREG.

```
STORE <OUT=>item-store-name
			</ LABEL='label'>;
```
			
* **item-store-name** is a usual one- or two-level SAS name, similar to the names that are used for SAS data sets
* **label** identifies the estimate on the output (is optional)

To perform post-fitting statistical analysis and plotting for the contents of the store item, you use the **PLM procedure**. The statements and options that are available vary depending upon which procedure you used to produce the item store.

```
PROC PLM RESTORE=item-store-specification <options>;
	EFFECTPLOT INTERACTION(SLICEBY=variable) <plot-type <(plot-definition options)>> / CLM </ options>;
	LSMEANS <model-effects> </ options>;
	LSMESTIMATE model-effect <'label'> values
		<divisor=n><,...<'label'> values
		<divisor=n> </ options>;
	SHOW options;
	SLICE model-effect / SLICEBY=variable ADJUST=tukey </ options>;
	WHERE expression;
RUN;
```

* **RESTORE** specifies the source item store for processing
* **EFFECTPLOT** produces a display of the fitted model and provides options for changing and enhancing the displays
* **LSMEANS** computes and compares least squares means (LS-means) of fixed effects
* **LSMESTIMATE**	provides custom hypothesis tests among least squares means
* **SHOW** uses ODS to display contents of the item store. This statement is useful for verifying that the contents of the item store apply to the analysis and for generating ODS tables.
* **SLICE** provides a general mechanism for performing a partitioned analysis of the LS-means for an interaction (analysis of simple effects) and it uses the same options as the LSMEANS statement
* **WHERE** is used in the PLM procedure when the item store contains **BY-variable** information and you want to apply the PROC PLM statements to only a subset of the BY groups

## Regression
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m553/m553_6_a_sum.htm)

![enter image description here](https://lh3.googleusercontent.com/-Y-23_trrs6A/WON7VhnzxPI/AAAAAAAAADY/vRxCZowh9tItWRSSGTHZYpa2Ur4Qh97nwCLcB/s0/summary3-6.png "Summary lessons 3-6")

### Exploratory Data Analysis

A useful set of techniques for investigating your data is known as **exploratory data analysis**.

***Scatter plots***

```
PROC SGSCATTER DATA=SAS-data-base;
	PLOT vairableY*(variableX1 variableX2) / REG;
RUN;
```

* If you have **so many observations** that the scatter plot of the whole data set is difficult to interpret, you might run PROC SGSCATTER on a **random sample of observations**

***Correlation analysis***

The closer the **Pearson** correlation coefficient is to +1/-1, the stronger the positive/negative linear relationship is between the two variables. The closer the correlation coefficient is to 0, the weaker the linear relationship and if it is 0 variables are uncorrelated.

* When you interpret the correlation, be cautious about the effect of **large sample sizes**: even a correlation of 0.01 can be statistically significant with a large enough sample size and you would almost always reject the hypothesis H0: &rho;=0, even if the value of your correlation is small for all practical purposes
* Some **common errors** on interpreting correlations are concluding a **cause-and-effect relationship** between the variables misinterpreting the kind of relationship between the variables and failing to recognize the influence of outliers on the correlation
	* The variables might be related but not causally
	* Correlation coefficients can be large because both variables are affected by other variables
	* Variables might be strongly correlated by chance
* Just because the correlation coefficient is close to 0 doesn't mean that no relationship exists between the two variables: they might have a **non-linear relationship**
* Another common error is failing to recognize the **influence of outliers** on the correlation
	* If you have an outlier you should report both correlation coefficients (with and without the outlier) to report how influential the unusual data point is in your analysis

The **PROC CORR** also produces **scatter plots** or a **scatter plot matrix**.

```
PROC CORR DATA=SAS-data-set RANK|NOSIMPLE PLOTS(ONLY)=MATRIX(NVAR=ALL HISTOGRAM)|SCATTER(NVAR=ALL ELLIPSE=NONE) <options>;
	VAR variable(s)X;
	WITH variable(s)Y;
	ID variable4label;
RUN;
```

### Simple Linear Regression
You use correlation analysis to determine the strength of the linear relationship between continuous response variables. Now you need to go a step further and **define the linear relationship itself**: $Y= \beta_0+\beta_1*X+\epsilon$

* $Y$ is the response variable 
* $X$ is the predictor variable
* $\beta_0$ is the intercept parameter
* $\beta_1$ is the slope parameter
* $\epsilon$ is the error term

The method of **least squares** produces parameter estimates $\hat \beta_0$ and $\hat \beta_1$ with certain **optimum properties** which make them the Best Linear Unbiased Estimators (**BLUE**):

* They are **unbiased estimates** of the population parameters
* They have **minimum variance**

To find out how much better is the model that takes the predictor variable into account than a model that ignores the predictor variable, you can compare the **simple linear regression model** to a **baseline model** ($Y= \bar Y$ independent of $X$). For your comparison, you calculate the **explained**, **unexplained** and **total variability** in the simple linear regression model.

* The **explained variability (SSM)** is the difference between the regression line and the mean of the response variable: $\sum(\hat Y_i-\bar Y)^2$
* The **unexplained variability (SSE)** is the difference between the observed values and the regression line: $\sum(Y_i-\hat Y_i)^2$
* The **total variability** is the difference between the observed values and the mean of the response variable: $\sum(Y_i-\bar Y)^2$

If we consider **hypothesis testing** for linear regression:

* H0: the regression model does not fit the data better than the baseline model (slope $= 0$)
* Ha: the regression model does fit the data better than the baseline model (slope $= \hat\beta_1 \ne 0$)

These **assumptions** underlie the hypothesis test for the regression model and have to be met for a simple linear regression analysis to be valid (last three assumptions are the same as for ANOVA):

* The mean of the response variable is linearly related to the value of the predictor variable
* The error terms are normally distributed with a mean of 0
* The error terms have equal variances
* The error terms are independent at each value of the predictor variable

```
PROC REG DATA=SAS-data-set <options>;
	MODEL dependent=regressor / CLM CLI </options>;
	ID regressor;
RUN;
QUIT;
```

To asses the level of precision around the mean estimates you can produce **confidence intervals** around the means. Confidence intervals become wider as you move away from the mean of the predictor variable. The wider the confidence interval the less precise it is. You might also want to construct **prediction intervals** for a single observation. A prediction interval is wider than a confidence interval because **single observations have more variability than sample means**.

For producing **predicted values** with PROC REG:

* Create a data set containing the values of the independent variables for which you want to make predictions
* Concatenate the new data set with the original data set
* Fit a simple linear regression model to the new data set and specify the **P** option in the MODEL statement

Because the concatenated observations contain **missing values** for the response variable, PROC REG does not include these observations when fitting the regression model. However, PROC REG does **produce predicted values** for these observations.

```
DATA SAS-predictions-data-set;
	INPUT dependent @@;
	DATALINES;
[new values separated with blanks]
;
RUN;

DATA SAS-new-data-set;
	SET SAS-predictions-data-set SAS-original-data-set;
RUN;

PROC REG DATA=SAS-new-data-set;
	MODEL dependent=regressor / P;
	ID regressor;
RUN;
QUIT;
```

When you use a model to predict future values of the response variable given certain values of the predictor variable, you must **stay within (or near) the range of values for the predictor variable used to create the model**. The relationship between the predictor variable and the response variable might be different beyond the range of the data.

If you have a large data set and have already fitted the regression model, you can predict values more efficiently by using **PROC REG** and **PROC SCORE**:

```
PROC REG DATA=SAS-original-data-set NOPRINT OUTEST=SAS-estimates-data-set;
	MODEL dependent=regressor </options>;
	ID regressor;
RUN;
QUIT;

PROC SCORE DATA=SAS-predictions-data-set
			SCORE=SAS-estimates-data-set
			OUT=SAS-scored-data-set
			TYPE=PARMS
			<options>;
	VAR variable(s);
RUN;
QUIT;
```

### Multiple Regression

In **multiple regression** you can model the relationship between the response variable and **more than one predictor variable**. It is a powerful tool for both **analytical or explanatory analysis and for prediction**.

$Y=\beta_0+\beta_1X_1+\beta_2X_2+...+\beta_kX_k+\epsilon$ ($k+1$ parameters)

***Advantages***

* Multiple linear regression is a more powerful tool
* You can determine whether a relationship exists between the response variable and more than one predictor variable at the same time

***Disadvantages***

* You need to perform a selection process to decide which model to use
* The more predictors you have, the more complicated interpreting the model becomes

If we consider **hypothesis testing** for linear regression:

* H0: the regression model does not fit the data better than the baseline model ($\beta_1=\beta_2=...=\beta_k= 0$)
* Ha: the regression model does fit the data better than the baseline model (at least one $\beta_i \ne 0$)

These **assumptions** have to be met for a multiple linear regression analysis to be valid (last three assumptions are the same as for ANOVA):

* A linear function of the $X$s accurately models the mean of the $Y$s
* The error terms are normally distributed with a mean of 0
* The error terms have constant variances
* The error terms are independent at each value of the predictor variable

The **regular $R^2$** values never decrease when you add more terms to the model, but the **adjusted $R^2$** value takes into account the number of terms in the model by including a penalty for the complexity of the model. The **adjusted $R^2$** value increases only if new terms that you add significantly improve the model enough to warrant increasing the complexity of the model. It enables proper comparison between models with different parameter counts. When an **adjusted $R^2$ increases by removing a variable** from the models, it strongly implies that the removed **variable was not necessary**.

``` 
PROC REG DATA=SAS-data-set <options>;
	MODEL dependent=regressor1 regressor2 </options>;
RUN;
QUIT;

PROC GLM DATA=SAS-data-set
		PLOTS(ONLY)=(CONTOURFIT);
		MODEL dependent=regressor1 regressor2;
		STORE OUT=SAS-multiple-data-set;
RUN;
QUIT;

PROC PLM RESTORE=SAS-multiple-data-set PLOTS=ALL;
	EFFECTPLOT CONTOUR (Y=regressor1 X=regressor2);
	EFFECTPLOT SLICEFIT (X=regressor2 SLICEBY=regressor1=250 to 1000 by 250);
RUN;
```

* In PROC GLM, when you run a linear regression model with only two predictor variables, the output includes a contour fit plot by default. We specify **CONTOURFIT** to tell SAS to overlay the contour plot with a scatter plot of the observed data

![enter image description here](https://lh3.googleusercontent.com/-NLdUzu7afu8/WOJldkIvudI/AAAAAAAAACs/Vo3RCSrIwvkcOUVi8mwEiurtjsOANjBTQCLcB/s0/contour-multiple-regression.png "Contour plot")

The plot shows **predicted values** of the response variable as **gradations of the background color** from blue, representing low values, to red, representing high values. The **dots**, which are similarly coloured, represent the **actual data**. Observations that are perfectly fit would show the same color within the circle as outside the circle. The **lines on the graph** help you read the actual predictions at even intervals.

* The **CONTOUR** option displays a contour plot of predicted values against two continuous covariates
* The **SLICEFIT** option displys a curve of predicted values vs a continuous variable grouped by the levels of another effect

Clearly the **PROC GLM** contour fit plot is **more useful**. However, if you do not have access to the original data set and can run **PROC PLM** only on the item store, this plot still gives you an idea of the relationship between the predictor variables and predicted values.
	
### Model Building and Interpretation

The brute force approach to find a good model is to start including all the predictor variables available and rerun the model **removing the least significant remaining term** each time **until** you're left with a model where **only significant terms remain**. With a small number of predictor variables a manual approach isn't too difficult but with a large number of predictor variables it's very tedious. Fortunately, if you specify the model selection technique to use, SAS finds good candidate models in an automatic way.

---

***All-possible regression methods***

SAS computes all possible models and ranks the results. Then, to evaluate the models, you compare statistics side by side ($R^2$, adjusted $R^2$ and $C_p$ statistic).

* **Mallows' $C_p$** statistic helps you detect model bias if you are underfitting/overfitting the model, it is a simple indicator of effective variable selection within a model
 
* To select the best model for prediction (most accurate model for predicting future values of $Y$), you should use the **Mallows' criterion**:  $C_p \le p$, which is the **number of parameters** in the model including the intercept
* To select the best model for parameter estimation (analytical or explanatory analysis), you should use **Hocking's criterion**: $C_p\le2p-p_{full}+1$

```
PROC REG DATA=SASdata-set PLOTS(ONLY)=(CP) <options>;
	<label:> MODEL dependent=regressors  / SELECTION=CP RSQUARE ADJRSQ BEST=n </options>;
RUN;
QUIT;
``` 

* **BEST** prints an specific number of the best candidate models according to a few different statistical criteria
* **SELECTION** option is used to specify the method used to select the model (**CP**, **RSQUARE** and **ADJRSQ** to calculate with the all-possible regression model; the first statistic determines the sorting order)
* For this all-possible regression model,we add the label **ALL_REG:**
* With **PLOTS=(CP)** we produce a plot:

![enter image description here](https://lh3.googleusercontent.com/-MKHCheN7vUA/WONM3WW0qSI/AAAAAAAAADE/tHJAjyHK-QE5j4UVlxmT7KHTA_bvGJLxwCLcB/s0/mallows_cp_best_model.png "Mallows' Cp to select the best model")

Each **star** represents the **best model** for a given number of parameters. The solid **blue line** represents **Mallows' criterion** for $C_p$, so using this line helps us find a good candidate model for prediction. Because we want the **smallest model possible**, we start at the left side of the graph, with the fewest number of parameters moving to the right until we find the **first model that falls below the solid blue line**. To find models for parameter estimation we have to look for models that falls below the **red solid line** which represent the **Hocking's criterion** for $C_p$ parameter estimation. If we hover over the star, we can see which variables are included in this model.

---

***Stepwise selection methods***

Here you choose a selection method (**stepwise**, **forward** or **backward** approaches) and SAS constructs a model based on that method. When you have a **large number of potential predictor variables**, the stepwise regression methods might be a better option. You can use either the **REG** procedure or the **GLMSELECT** procedure to perform stepwise selection methods

* **Forward selection** starts with no predictor variables in the model
 1. It selects the best one-variable model
 2. It selects the best two-variable model that includes the variable from the first model (after a variable is added to the model, it stays in even if it becomes insignificant later)
 3. It keeps adding variables, one at a time, until no significant terms are left to add
* **Backward selection/elimination** starts with all predictor variables in the model
 1. It removes variables one at a time, starting with the most non-significant variable (after a variable is removed from the model, it cannot reenter)
 2. It stops when only significant terms are left in the model
* **Stepwise selection** combines aspects of both forward and backward selection
 1. It starts with no predictor variables in the model and starts adding variables, one at a time, as in forward selection
 2. However, as in backward selection, stepwise selection can drop non-significant variables, one at a time
 3. It stops when everything in the model is currently significant and everything not in the model is not significant

Statisticians in general agree on first using **stepwise methods** to identify several good candidates models and then applying your **subject matter expertise** to choose the best model. Because the techniques for selecting or eliminating variables differ between the three selection methods, **they don't always produce the same final model**. There is no one method that is best and **you need to be cautious** when reporting statistical quantities produced by these methods:

* Using automated model selections results in **biases in parameter estimates**, **predictions** and **standard errors**
* **Incorrect** calculation of **degrees of freedom**
* **p-values** that tend to err on the side of **overestimating significance**

How can you **avoid these issues**?

* You can hold out some of your data in order to perform an honest assessment of how well your model performs on a different sample of data (**holdout/validation data**) than you use to develop the model (**training data**)
* Other honest assessment approaches include **cross-validation** (if your data set is not large enough to split) or **bootstraping** (a resampling method that tries to approximate the distribution of the parameter estimates to estimate the standard error and p-values)

```
PROC GLMSELECT DATA=SAS-data-set <options>;
	CLASS variables;
	<label:> MODEL dependent(s) = regressor(s) / </options>;
RUN;
QUIT;
```

* The **SELECTION** option specifies the method to be used to select the model (**FORWARD** | **BACKWARD** | **STEPWISE** = default value)
* The **SELECT** option specifies the criterion to be used to determine which variable to add/remove from the model (**SL** = significance level as the selection criterion)
* The **SLENTRY** option determines the significance level for a variable to enter the model (default = 0.5 for forward and 0.15 for stepwise)
* The **SLSTAY** option determines the significance level for a variable to stay in the model (default = 0.1 for backward and 0.15 for stepwise)
* You can display p-values in the *Parameter Estimates* table by including the **SHOWPVALUES** option int he MODEL statement
* The **DETAILS** option specifies the level of detail produced (**ALL** | **STEPS** | **SUMMARY**)

---

Recommendations to decide which model is best for your needs:

1. Run all model selection methods
2. Look for commonalities across the results 
3. Narrow down your choice of models by using your subject matter knowledge

### Information Criterion and Other Selection Options

There are other selection criteria that you can use to select variables for a model as well as evaluate competing models. These statistics are collectively referred to as **information criteria**. Each information criterion searched for a model that minimizes the **unexplained variability** with as **few effects in the model as possible**. The model with the **smaller information criterion is considered to be better***. For types are available in **PROC GLMSELECT**:

* Akaike's information criterion (SELECT=**AIC**)
* Correcterd Akaike's information criterion (SELECT=**AICC**)
* Sawa Bayesian information criterion (SELECT=**BIC**)
* Schwarz Bayesian information criterion (SELECT=**SBC**, it could be called **BIC** in some other SAS procedures)

The calculations of all information criteria begin the same way:

1. First you calculate $n\cdot log(SSE/n)$ 
2. Then, each criterion adds a penalty that represents the complexity of the model (each type of information criterion invokes a different penalty component)
 * AIC: $2p+n+2$
 * AICC: $n(n+p)/(n-p-2)$
 * BIC: $2(p+2)1-2q^2$
 * SBC: $p\cdot log(n)$

##Model Post-Fitting for Inference
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m554/m554_4_a_sum.htm)

How to **verify the assumptions** and **diagnose problems** that you encounter in **linear regression**?

###Examining Residuals
You can use the **residual values** (difference between each observed value of $Y$ and its predicted value) from the regression analysis to verify the **assumptions of the linear regression**. Residuals are estimates of the errors, so you can **plot the residuals to check the assumptions of the errors**.

* You can plot residuals vs the predicted values to check for **violations of equal variances**
* You can also use this plot to check for **violations of linearity and independence**
* You can plot the residuals vs the values of the independent variables to **further examine any violations of equal variances** (you can see which predictor contributes to the violation of the assumption)
* You can use a histogram or a normal probability plot of the residuals to determine whether or not the **errors are normally distributed**

You want to see a **random scatter of the residual values** above and below the reference line at 0. If you see **patterns or trends** in the residual values, the assumptions might not be valid and the models might have problems.

![enter image description here](https://lh3.googleusercontent.com/-84ce_WbduHI/WOOAkeq-iiI/AAAAAAAAADs/e2ZzJE_XoLE8DnfdqNt-aaHhOzw8Z-ucgCLcB/s0/assumption-violation.PNG "Assumptions violation examples")

**Note**: To take autocorrelation (correlated over time) into account, you might need to use a regression procedure such as **PROC AUTOREG**

You can also use these plots to **detect outliers**, which often reflect data errors or unusual circumstances. They can affect your regression results, so you want to know whether any outliers are present and causing problems and investigate if they result from **data entry error or some other problem** that you can correct.

```
PROC REG DATA=SAS-data-set PLOTS(ONLY)=(QQ RESIDUALBYPREDICTED RESIDUALS)<options>;
	<label:> MODEL dependent=regressor(s) </options>;
	ID variable4identification;
RUN;
QUIT;
```

* **QQ** requests a residual quantile-quantile plot to assess the normality of the residual error
* **RESIDUALBYPREDICTED** requests a plot of residuals by predicted values to verify the equal variance assumption, the independence assumption and model adequacy
* **RESIDUALS** requests a panel of plots of residuals by the predictor variables in the model. If any of the *Residual by Regressors* plots show signs of unequal variance, we can determine which predictor variable is involved in the problem.

###Identifying Influential Observations

An influential observation is different from an outlier. An **outlier** is an unusual observation that has a large residual compare to the rest of the points. An **influential observation** can sometimes have a large residual compared to the rest of the points, but it is an observation so far away from the rest of the data that it singlehandedly exerts influence on the slope of the regression line.

![enter image description here](https://lh3.googleusercontent.com/-SRVHsMC3NQg/WOOVPEZxDLI/AAAAAAAAAEA/1vGwrG2XlWISRMTx2fguuh109zyLXfVGACLcB/s0/outlier_vs_influential-observation.PNG "outlier vs influential observation")

---
***Using STUDENT residuals to detect outliers***

Also known as **studientized or standardized residuals**, the STUDENT residuals are calculated by dividing the **residual by their standard errors**, so you can think of them as roughly equivalent to a z-score. 

* For **relatively small sample sizes**, if the absolute value of the STUDENT **residual is $>2$**, you can suspect that the corresponding observation is an outlier
* For **large sample sizes**, it's very likely that even more STUDENT **residuals greater than $\pm2$** will occur just by chance, so you should typically use a larger cutoff value of $>3$

---
***Using Cook's D statistics to detect influential observations***

Fore each observation, the Cook's D statistic is **calculated as if that observation weren't in the data set** as well as the set of parameter estimates with all the observations in your regression analysis. 

* If any observation has a Cook's D **statistic $>4/n$** that observation is influential
* The Cook's D statistic is most useful for identifying influential observations when the purpose of your model is **parameter estimation**

---
***Using RSTUDENT residuals to detect influential observations***

RSTUDENT residuals are similar to STUDENT residuals. For each observation, the RSTUDENT residual is the **residual divided by the standard error estimated with the current observation deleted**.

* If the RSTUDENT residual is different from the STUDENT residual, the observation is probably influential
* If the absolute value of the RSTUDENT residuals is $>2$ or $>3$, you've probably detected an influential observation

---
***Using DFFITS statistics to detect influential observations***

DFFITS measures the impact that each observation has on its own predicted value. For each observation, DFFITS is **calculated using two predicted values**:

* The first predicted value is calculated from a model using the entire data set to estimate model parameters
* The second predicted value is calculated from a model using the data set with that particular observation removed to estimate model parameters
* The difference between the two predicted values is divided by the standard error of the predicted value, without the observation

If the **standardized difference** between these predicted values **is large**, that particular observation has a **large effect on the model fit**.

* The **general cutoff** value is $2$
* The more **precise cutoff** is $2 \cdot sqrt(p/n)$
* If the absolute value of DFFITS for any observation is $>$ cutoff value, you've detected an influential observation
* DFFITS is most useful for **predictive models**

---
***Using DFBETAS statistics to explore the influenced predictor variable***

To help identifying which parameter the observation might be influencing most you can use DFBETAS (difference in betas). It measure the change in each parameter estimate. 

* One DFBETA is calculated per predictor variable per observation
* Each value is calculated by taking the estimated coefficient for that particular predictor variable **using all the data**, subtracting the estimated coefficient for that particular predictor variable with the **current observation removed** and dividing by its standard error
* Large DFBETAS indicate observations that are influential in estimating a given parameter:
 * The **general cutoff** value is $2$
 * The more **precise cutoff** is $2 \cdot sqrt(1/n)$

---
```
PROC GLMSELECT DATA=SAS-data-set <options>;
	<label:> MODEL dependent(s) = regressor(s) / </options>;
RUN;
QUIT;

ODS OUTPUT RSTUDENTBYPREDICTED=name-rstud-data-set
	       COOKSDPLOT=name-cooksd-data-set
	       DFFITSPLOT=name-dffits-data-set
	       DFBETASPANEL=name-dfbs-data-set;
			   
PROC REG DATA=SAS-data-set PLOTS(ONLY LABEL)=
								(RSTUDENTBYPREDICTED 
								 COOKSD 
								 DFFITS 
								 DFBETAS) <options>;
	<label:> MODEL dependent=&_GLSIND </options>;
		ID variable4identification;
RUN;
QUIT;
	
DATA influential;
	MERGE name-rstud-data-set
		  name-cooksd-data-set
		  name-dffits-data-set
		  name-dfbs-data-set;
	BY observation;
		
	IF (ABS(RSTUDENT)>3) OR (COOKSDLABEL NE ' ') OR DFFITSOUT THEN FLAG=1;
	ARRAY DFBETAS{*} _DFBETASOUT: ;
	DO I=2 TO DIM(DFBETAS);
		IF DFBETAS{I} THEN FLAG=1;
	END;
		
	IF ABS(RSTUDENT)<=3 THEN RSTUDENT=.;
	IF COOKSDLABEL EQ ' ' THEN COOKSD=.;

	IF FLAG=1;
	DROP I FLAG;
RUN;
	
PROC PRINT DATA=influential;
	ID observation;
	VAR RSTUDENT COOKSD DFFITSOUT _DFBETASOUT: ;
RUN;
```
* PROC GLMSELECT automatically creates the **&_GLSIND** macro variable which stores the list of effects that are in the model whose variable order you can check in the *Influence Diagnostics* panel
* The **ODS** statement takes the data that creates each of the requested plots and saves it in the specified data set
* The **LABEL** option includes a label for the extreme observations in the plot (labeled with the observation numbers if there is not ID specified)

Having **influential observations doesn't violate regression assumptions**, but it's a major nuisance that you need to address:

 1. **Recheck** for data entry errors
 2. If the data appears to be valid, **consider whether you have an adequate model** (a different model might fit the data better). Divide the number of influential observations you detect by the number of observations in you data set: if the result is **$>5\%$ you probably have the wrong model**.
 3. Determine whether the influential observation is **valid but just unusual**
 4. As a general rule you should **not exclude data** (some unusual observations contain important information)
 5. If you choose to exclude some observations, include in your report a **description of the types of observations that you excluded and why** and discuss the limitation of the conclusions given the exclusions

###Detecting Collinearity

Collinearity (or multicollinearity) is a problem that you face in multiple regression. It occurs when two or more **predictor variables are highly correlated with each other** (**redundant information** among them, the predictor variables explain much of the same variation in the response). Collinearity doesn't violate the assumptions of multiple regression.

* Collinearity can **hide significant effects** (if you include only one of the collinear variables in the model it is significant but when there are more than one included none of them are significant)
* Collinearity **increases the variance** of the parameter estimates, making them **unstable** (the data points don't spread out enough in the space to provide stable support for the plane defined by the model) and, in turn, this **increases the prediction error** of the model

When an overall model is highly significant but the individual variables don't tell the same story, it's a **warning sign of collinearity**. When the **standard error for an estimate is larger than the parameter estimate** itself, it's not going to be statistically significant. The SE tells us how variable the corresponding parameter estimate is: when the standard errors are high, the **model lacks stability**.

```
PROC REG DATA=SAS-data-set <options>;
	<label:> MODEL dependent = regressors / VIF </options>;
RUN;
QUIT;
```

* The **VIF** (variance inflation factor, $VIF_i=1/(1-R_i^2)$) option measures the magnitude of collinearity in a model (VIF$>10$ for any predictor in the model, those predictors are probably involved in collinearity)
* Other options are **COLLIN** (includes the intercept when analyzing collinearity and helps identify the predictors that are causing the problem) and **COLLINOINT** (requests the same analysis as COLLIN but excludes the intercept)

---

***Effective modeling cycle***

 1. You want to get to know your data by **performing preliminary analysis**: 
	 * Plot your data
	 * Calculate descriptive statistics 
	 * Perform correlation analysis
 2. Identify some **good candidate models** using PROC REG: 
 * First check for collinearity 
 * Use all-possible regression or stepwise selection methods and subject matter knowledge to select model candidates
 * Identify the good ones with the Mallows' (prediction) or Hocking's (explanatory) criterion for $C_p$
 3. **Check and validate your assumtions** by creating residual plots and conducting a few other statistical tests
 
 4. Deal with any **problems in your data**: 
  * Determine whether any influential observations might be throwing off your model calculations
  * Determine whether any variables are collinear
 5. **Revise your model**
 
 6. **Validate your model** with data not used to build the  model (prediction testing)

##Categorical Data Analysis
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m556/m556_5_a_sum.htm)

When you response variable is categorical, you need to use a different kind of regression analysis: **logistic regression**.

###Describing Categorical Data

When you examine the distribution of a **categorical variable**, you want to know the **values** of the variable and the **frequency or count** of each value in the data (**one-way frequency able**).

```
PROC FREQ DATA=SAS-data-set;
	TABLES variable1 variable2 variable3 </options>;
	<additional statements>
RUN;
```

To look for a possible **association** between two or more categorical variables, you can create a **crosstabulation**/**contingency table** (when it displays statistics for two variables is also called **two-way frequency able**).

```
PROC FREQ DATA=SAS-data-set;
	TABLES variable-rows*variable-columns </options>;
		<additional statements>
RUN;
```

Two distribution plots are associated with a frequency or crosstabulation table: a **frequency plot**, PLOTS=**(FREQPLOT)**, and a **cumulative frequency plot**.

In PROC FREQ output, the default order for character values is **alphaumeric**. To reorder the values of an ordinal variable in your FROC FREQ output you can:

* Create a **new variable** in which the values are stored in logical order
* Apply a [**temporary format**](https://support.sas.com/edu/OLTRN/ECST131/eclibjr/tempformat.htm) to the original variable

###Tests of Association

To perform a **formal test of association** between two categorical variables, you use the (Pearson) **chi-square test** which measures the difference between the observed cell frequencies and the cell frequencies that are expected if there is no association between variables (H0 is true): 
$Expected=Row \ total\cdot Column\ total/Total \ sample \ size$

* If the **sample size decreases**, the **chi-square value decreases** and the **p-value for the chi-square statistic increases**
* Hypothesis testing: **H0**: no association; **Ha**: association

**Cramer's V statistic** is one measure of strength of an association between two categorical variables

 * For two-by-two tables, Cramer's V is in the range of -1 to 1
 * For larger tables, Cramer's V is int he range of 0 to 1 
 * Values farther away from 0 indicate a relatively strong association between the variables

To measure the strength of the association between a binary predictor variable and a binary outcome variable, you can use an **odds ratio**: $Odds \ Ratio=\frac{Odds \ of \ Outcome \ in \ Group \ B}{Odds \ of \ Outcome \ in \ Group \ A}$; $Odds=p_{event}/(1-p_{event})$

 * The value of the odds ratio can range from 0 to $\infty$; it cannot be negative
 * When the odds ratio is $1$, there is no association between variables
 * When the odds ratio $>1$/$<1$, the group in the numerator/denominator is more likely to have the outcome
 * The odds ratio is approximately the same **regardless of the sample size**
 * To estimate the true odds ratio while taking into account the variability of the sample statistic, you can calculate **confidence intervals**
 * You can use an odds ratio to **test for significance** between two categorical variables
 * Odds ratio expressed as percent difference: $(odd \ ratio -1) \cdot 100$

```
PROC FREQ DATA=SAS-data-set;
	TABLES variable-rows*variable-columns / CHISQ EXPECTED </options>;
	<additional statements>
RUN;
```

* **CHISQ** produces the Pearson chi-square test of association, the likelihood-ratio chi-square and the Mantel-Haenszel: $\sum \frac{(obs. \ freq. - exp. \ freq.)^2}{exp. \ freq.}$
* **EXPECTED** prints the expected cell frequencies
* **CELLCHI2** prints each cell's contribution to the total chi-square statistic: $ \frac{(obs. \ freq. - exp. \ freq.)^2}{exp. \ freq.}$
* **NOCOL** suppresses the printing of the column percentages
* **NOPERCENT** supresses the printing of the cell percentages
* **RELRISK** (relative risk) prints a table that contains risk ratios (probability ratios) and odds ratios; PROC FREQ uses the **classification in the first column** of the crosstabulation table as the **outcome of interest** and the first/second row in the numerator/denominator

---

For **ordinal associations**, the **Mantel-Haenszel** chi-square test is a more powerful test.

* The levels must be in a **logical order** for the test results to be meaningful
* Hypothesis testing: **H0**: no ordinal association; **Ha**: ordinal association
* Similarly to the Pearson case, the Mantel-Haenszel chi-square statistic/p-value indicate whether an association exists but not its magnitude and they depend on and reflect the sample size

To measure the **strength of the association** between two ordinal variables you can use the **Spearman correlation** statistic.

* You should only use it if both variables are ordinal and are in logical order
* Is considered to be a rank correlation because it provides a degree of association between the ranks of the ordinal variables
* This statistic has a **range between -1 and +1**: values close to -1/+1 indicate that there is a relatively high degree of negative/positive correlation and values close to 0 indicate a weak correlation
* It is **not affected by the sample size**

```
PROC FREQ DATA=SAS-data-set;
	    TABLES variable-rows*variable-columns / CHISQ EXPECTED </options>;
	    <additional statements>
RUN;
```

* **MEASURES** produces the Spearman correlation statistic along with other measurement of association
* **CL** produces confidence bounds for the statistics that the MEASURES option requests
* The confidence bounds are valid only if the sample size is large ($>25$)
* The asymptotic standard error (**ASE**) is used for large samples and is used to calculate the confidence intervals for various measures of association (including the Spearman correlation coefficient)

###Introduction to Logistic Regression

Logistic Regression is a generalized linear model that you can use to predict a categorical response/outcome on the basis if one or more continuous or categorical predictor variables. There are three models:

![enter image description here](https://lh3.googleusercontent.com/-_wxj3yC7ZCE/WOd2RpxTQOI/AAAAAAAAAEg/HYmLKYjBYr8Thq7HwseFdK3hU8Tnreo8ACLcB/s0/logistic_regression_types.PNG "Logistic regression types")

Some reasons why you **can't use linear regression** with a **binary response variable** are:

* 

###Multiple Logistic Regression


## Model Building and Scoring for Prediction
[Chapter summary in SAS](https://support.sas.com/edu/OLTRN/ECST131/m555/m555_3_a_sum.htm)

###Introduction to Predictive Modeling

###Scoring Predictive Models

# Missing data

Depending on the **type of data and model** you will be using, techniques such as **multiple imputation** or **direct maximum likelihood** may better serve your needs. The main goals of statistical analysis with missing data are:

* Minimize bias
* Maximize use of available information
* Obtain appropriate estimates of uncertainty

To use the more appropriate imputation method you should consider the missing data mechanism of your data which describes the process that is believed to have generated the missing values:

* **Missing completely at random (MCAR)**:  neither the variables in the dataset nor the unobserved value of the variable itself predict whether a value will be missing
* **Missing at random (MAR)**: other variables (but not the variable itself) in the dataset can be used to predict missingness on a given variable
* **Missing not at random (MNAR)**: value of the unobserved variable itself predicts missingness

Imputed values are **not** equivalent to observed values and serve only to help estimate the covariances between variables needed for inference.

Some of the imputation techniques are:

* **Complete case analysis (listwise deletion)**:  deleting cases in a particular dataset that are missing data on any variable of interest (for MCAR cases the power is reduced but it does not add any bias) 
* **Available case analysis (pairwise deletion)**:  deleting cases where a variable required for a particular analysis is missing, but including those cases in analyses for which all required variables are present
* **Mean Imputation**:
* **Single Imputation**:
* **Stochastic Imputation**: 

## Direct maximum likelihood

## Multiple imputation

Multiple Imputation is always superior to any of the single imputation methods because:

* A single imputed value is never used
* The variance estimates reflect the appropriate amount of uncertainty surrounding parameter estimates

There are several decisions to be made before performing a multiple imputation including **distribution**, **auxiliary variables** and **number of imputations** that can affect the quality of the imputation.

1. **Imputation phase (PROC MI)**:  the user specifies the imputation model to be used and the number 
	   of imputed datasets to be created
2. **Analysis phase (PROG GLM/PROC GENMOD)**: runs the analytic model of interest within each of the imputed datasets
3. **Pooling phase (PROC MIANALYZE)**: combines all the estimates across all the imputed datasets and outputs one set of parameter estimates for the model of interest

***MVN or FCS?***


***Auxiliary variables***

* They can can help improve the likelihood of meeting the MAR assumption 
* They help yield more accurate and stable estimates and thus reduce the estimated standard errors in analytic models 
*  Including them can also help to increase power

***Number of imputations (m)***

* Estimates of coefficients stabilize at much lower values of *m* than estimates of variances and covariances of error terms 
* A larger number of imputations may also allow hypothesis tests with less restrictive assumptions (i.e., that do not assume equal fractions of missing information for all coefficients)
* Multiple runs of m imputations are recommended to assess the stability of the parameter estimates
*  Recommendations: 
 *  For low fractions of missing information (and relatively simple analysis techniques) 5-20 imputations and 50 or more when the proportion of missing data is relatively high
 *  The number of imputations should equal the percentage of incomplete cases (*m*=max(FMI%)), this way the error associated with estimating the regression coefficients, standard errors and the resulting p-values is considerably reduced and results in an adequate level of reproducibility

**More comments**

* You should include the dependent variable (DV) in the imputation model unless you would like to impute independent variables (IVs) assuming they are uncorrelated with your DV
* Although MI can perform well up to 50% missing observations,  the larger the amount the higher the chance of finding estimation problems during the imputation process and the lower the chance of meeting the MAR assumption⧸⧸

# Macros

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

# Misc

When you name a process flow Autoexec, SAS Enterprise Guide prompts you to run the process flow when you open the project. This makes it easy to recreate your data when you start practising in the course.

For the SAS Enterprise Guide you need to add this command to get the plots displayed in the output:

```
ODS GRAPHICS ON;
[your code here]
ODS GRAPHICS OFF;
```

When you add the **ODS TRACE** statement, SAS writes a trace record to the log that includes information about each output object (name, label, template, path):

``` 
ODS TRACE ON;
[your code here]
ODS TRACE OFF;
```

You produce a list of the possible output elements in the log that you may specify in the **ODS SELECT/EXCLUDE** statement:

```
ODS SELECT lmeans diff meanplot diffplot controlplot;
[your code here]
```
This way you can see the actual variable level values in the output rather than some indexes:
```
FORMAT variable DOSEF.;
```

* Copy database **test** into **work**:

```
proc copy in=test out=work;
run;
```

* Declaring **arrays**:

The dimension has to be known in advance (???)
There's no way to write an implicit loop through all the elements of the array (???)

```
data _null_;

	ARRAY arrayname[2,3] <$> v11-3 (0 0 0)
	                     <$> v21-3 (0 0 0);
                             
	DO i=1 TO DIM(arrayname);
		arrayname[i] = arrayname[i] + 1;
	END;

	result=CATX(',',OF v11-3);
	PUT result=;

RUN;
```

IF THEN analogy to "CONTAINS"

```
if find(variable_name,'pattern') then
```

Site calculation from the two first numbers of the patient number:
```
site = SUBSTR(PUT(patient,z4.),1,2);
```
* **PUT**: turns the numeric variable *patient* into a string (**z4.** adds leading zeroes if needed)
* **SUBSTR**: takes the first **2** characters starting from position **1**

[Count the distinct values of a variable](http://support.sas.com/kb/36/898.html)



