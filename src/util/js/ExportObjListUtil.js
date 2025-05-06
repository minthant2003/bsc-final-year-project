window.exportObjListToExcel = (objList, headerArr, keyArr, sheetName) => {
    // filter some obj attributes from objList
    const modifiedObjList = objList?.map(obj => keyArr?.map(key => obj[key]));
    const excelData = [headerArr, ...modifiedObjList];
    const excelSheet = XLSX.utils.aoa_to_sheet(excelData);
    const excelBook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(excelBook, excelSheet, sheetName);

    XLSX.writeFile(excelBook, `${sheetName}_${new Date()}_export.xlsx`);
}