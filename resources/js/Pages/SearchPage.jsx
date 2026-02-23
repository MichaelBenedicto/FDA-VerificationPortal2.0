import React, { useState } from "react";

const SORTABLE_COLUMNS = [
  "ESTABLISHMENT_NAME",
  "ESTABLISHMENT_OWNER",
  "PRODUCT_NAME",
  "BRAND_NAME",
  "COMPANY_NAME",
  "generic_name",
  "brand_name",
  "registration_number",
  "product_name",
  "company_name",
  "license_number",
  "name_of_establishment",
  "owner",
  "batch_notification_number",
  "lot_release_number",
  "usage",
  "CPR_NUMBER",
  "CERT_NUM",
  "MANUFACTURER_NAME",
  "LTO_NO",
  "FERN_NO",
  "PRODUCT_BRAND_NAME",
  "PRODUCT",
  "title",
  "category",
  
];


export default function SearchPage() {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState({
    lto_food: [],
    lto_drugs: [],
    lto_medicaldevice: [],
    lto_healthrelateddevice: [],
    lto_pco: [],
    lto_cosmetics: [],
    lto_hup: [],
    lto_tcca: [],
    fdafoodproducts: [],
    cdrr: [],
    cpr_cdrrhr: [],
    healthcare_waste: [],
    water_purification: [],
    xray: [],
    csl_batch: [],
    csl_lot: [],
    vat_exempt: [],
    cosmetic_NN: [],
    cmdn: [],
    localcgmp: [],
    desktopForeigncgmp:[],
    inspectedForeign: [],
    PermitToRegister: [],
    lto_huhs: [],
    cpr_hup:[],
    cpr_huhs: [],
    tcca_notif: [],
    food_gmp: [],
    HACCP: [],
    HACCPprod: [],
    fdawebsite: [],
    tcca_notif_products: [],
    otherEST: [],
  });

  const [sortConfig, setSortConfig] = useState({
  key: null,
  direction: "asc", // or "desc"
});

const handleSort = (column) => {
  setSortConfig((prev) => {
    if (prev.key === column) {
      return {
        key: column,
        direction: prev.direction === "asc" ? "desc" : "asc",
      };
    }
    return { key: column, direction: "asc" };
  });
};

  const [hasSearched, setHasSearched] = useState(false);
  const [loading, setLoading] = useState(false);
  const [listening, setListening] = useState(false);
  const [activeTab, setActiveTab] = useState("lto_food");

  const itemsPerPage = 10;

  const [pagination, setPagination] = useState({
    lto_food: 1,
    lto_drugs: 1,
    lto_medicaldevice: 1,
    lto_healthrelateddevice: 1,
    lto_pco: 1,
    lto_cosmetics: 1,
    lto_hup: 1,
    lto_tcca: 1,
    fdafoodproducts: 1,
    cdrr: 1,
    cpr_cdrrhr: 1,
    healthcare_waste: 1,
    water_purification: 1,
    xray: 1,
    csl_batch: 1,
    csl_lot: 1,
    vat_exempt: 1,
    cosmetic_NN: 1,
    cmdn: 1,
    localcgmp: 1,
    desktopForeigncgmp: 1,
    inspectedForeign: 1,
    PermitToRegister: 1,
    lto_huhs: 1,
    cpr_hup: 1,
    cpr_huhs: 1,
    tcca_notif: 1,
    food_gmp: 1,
    HACCP: 1,
    HACCPprod: 1,
    otherEST: 1,
    fdawebsite: 1,
    tcca_notif_products: 1,
  });

  const [expandedRows, setExpandedRows] = useState({
    lto_food: [],
    lto_drugs: [],
    lto_medicaldevice: [],
    lto_healthrelateddevice: [],
    lto_pco: [],
    lto_cosmetics: [],
    lto_hup: [],
    lto_tcca: [],
    cdrr: [],
    cpr_cdrrhr: [],
    healthcare_waste: [],
    water_purification: [],
    xray:[],
    csl_batch:[],
    csl_lot:[],
    vat_exempt:[],
    cosmetic_NN: [],
    cmdn: [],
    localcgmp: [],
    desktopForeigncgmp: [],
    inspectedForeign: [],
    PermitToRegister: [],
    lto_huhs: [],
    cpr_hup: [],
    cpr_huhs: [],
    tcca_notif: [],
    food_gmp: [],
    HACCP: [],
    HACCPprod: [],
    otherEST: [],
    fdawebsite: [],
    tcca_notif_products: [],
  });

 
  // Columns & Labels

  const ltoColumns = [
    "LTO_NUMBER",
    "ESTABLISHMENT_NAME",
    "ESTABLISHMENT_OWNER",
    "ESTABLISHMENT_STATUS",
  ];

  const fdaFoodColumns = [
    "ACCOUNTCODE",
    "PRODUCT_NAME",
    "BRAND_NAME",
    "COMPANY_NAME",
    "DENIAL_DATE",
    "DATE_VALIDITY",
  ];

  const cdrrColumns = [
    "registration_number",
    "generic_name",
    "brand_name",
    "dosage_strength",
    "dosage_form",
    "classification",
  ];

  const cdrrhrcprColumns = [
    "registration_number",
    "product_name",
    "manufacturer",
  ];

  const healthcare_wasteColumns = [
    "registration_number",
    "product_name",
    "company_name",
  ];

  const water_purificationColumns = [
    "registration_number",
    "product_name",
    "company_name",
  ];

  const xrayColumns = [
    "license_number",
    "name_of_establishment",
    "owner",
    "classification",
  ];

  const cslbatchColumns = [
    "batch_notification_number",
    "generic_name",
    "brand_name",
    "batch_number",
    "registration_number",
  ];

  const csllotColumns = [
    "lot_release_number",
    "generic_name",
    "brand_name",
    "batch_lot_number",
    "registration_number",
  ];

  const vat_exemptColumns = [
    "usage",
    "generic_name",
    "dosage_strength",
    "dosage_form",
    "date_publication",
    ];

  const cosmetic_NNColumns = [
    "ACCOUNTCODE",
    "PRODUCT_NAME",
    "BRAND_NAME",
    "COMPANY_NAME",
    ];
    
  const cmdnColumns = [
    "CPR_NUMBER",
    "PRODUCT_NAME",
    "COMPANY_NAME",
    "DECISION_DATE",
    "DATE_VALIDITY",
    ];

  const localcgmpColumns = [
    "CERT_NUM",
    "COMPANY_NAME",
    "CATEGORY",
    "VALIDITY_DATE",
    ];

  const desktopForeigncgmpColumns = [
    "CERT_NUM",
    "MANUFACTURER_NAME",
    "PLANT_ADDRESS",
    "IMPORTER_NAME",
    ];

  const inspectedForeignColumns = [
    "CERT_NUM",
    "MANUFACTURER_NAME",
    "PLANT ADDRESS",
    "IMPORTER_NAME",
    ];

  const PermitToRegisterColumns = [
    "CERT_NUM",
    "MANUFACTURER_NAME",
    "IMPORTER_NAME",
    ];
    
  const cprhupColumns = [
    "registration_number",
    "product_name",
    "manufacturer",
    "distributor",
    ];

  const cprhuhsColumns = [
    "FERN_NO",
    "PRODUCT_NAME",
    "DATE_DECISION_HUMAN",
    "FERN_EXPIRY_HUMAN",
    ];

  const tccanotifColumns = [
    "ACCOUNTCODE",
    "PRODUCT_BRAND_NAME",
    "COMPANY_NAME",
    "NOTIFICATION_VALIDITY",
    ];

  const foodgmpColumns = [
    "ACCOUNTCODE",
    "ESTABLISHMENT_NAME",
    "ESTABLISHMENT_OWNER",
    "VALIDITY",
    ];

  const haccpColumns = [
    "ACCOUNTCODE",
    "ESTABLISHMENT_NAME",
    "PLANT_ADDRESS",
    ];

  const haccpprodColumns = [
    "ACCOUNTCODE",
    "PRODUCT",
    "VALIDITY_DATE",
    ];

  const fdawebsiteColumns = [
    "date_posted",
    "title",
    "category",
    "post_link",
    ];

    const tccaProdColumns = [
    "ITEM_NAME",
    "ITEM_MODEL_NO",
    "ROW",
    "ITEM_SKU",
    "ITEM_AGE_GRADING_LABEL",
    
    ];


  const labelMap = {
    // Common LTO
    LTO_NUMBER: "Licensed Number",
    ESTABLISHMENT_NAME: "Establishment Name",
    ESTABLISHMENT_OWNER: "Owner",
    PRIMARY_ACTIVITY: "Primary Activity",
    ADDITIONAL_ACTIVITIES: "Additional Activities",
    ADDRESS: "Address",
    REGION: "Region",
    ACCOMPLISHED_DATE: "Issuance Date",
    ISSUANCE_DATE: "Issuance Date",
    LTO_VALIDITY: "Validity Date",
    ESTABLISHMENT_STATUS: "Status",
    SCOPE_OF_WORK: "Scope of Work",
    PCO_METHOD: "PCO Method",
    LTO_ACTIVITY_LABEL: "Activity",
    LTO_DECISION: "Decision",
    LTO_NO: "Licensed Number",
    LTO_PREFERRED_ADDRESS_LABEL: "Address",
    PRODUCT_CLASSIFICATION_LABEL: "Classification",
    PRIMARY_ACTIVITY_LABEL:"Primary Activity",
    SECONDARY_ACTVITY_STRING:"Additional Activities",
    DATE_DECISION_HUMAN: "Issuance Date",
    LTO_EXPIRY_HUMAN: "Validity Date",
    APPLICATION_TYPE: "Application Type",

    // FDA Food Products
    ACCOUNTCODE: "Registration Number",
    PRODUCT_NAME: "Product Name",
    BRAND_NAME: "Brand Name",
    COMPANY_NAME: "Company Name",
    DENIAL_DATE: "Issuance Date",
    DATE_VALIDITY: "Expiry Date",

    // CDRR CPR
    registration_number: "Registration Number",
    generic_name: "Generic Name",
    brand_name: "Brand Name",
    dosage_strength: "Dosage Strength",
    dosage_form: "Dosage Form",
    classification: "Classification",
    packaging: "Packaging",
    country_of_origin: "Country of Origin",
    trader: "Trader",
    importer: "Importer",
    distributor: "Distributor",
    app_type: "Application Type",
    expiry_date: "Expiry Date",
    pharmacologic_category: "Pharmacologic Category",

    //COMMON CDRRHR CPR
    product_name: "Product Name",
    manufacturer: "Manufacturer",
    issuance_date: "Issuance Date",
    company_name: "Company Name",
    intended_use_claim: "Intended Use Claim",
    license_number: "License Number",
    name_of_establishment: "Establishment Name",
    owner: "Owner",
    address: "Address",

    //CSL
    batch_notification_number: "Batch Notification Number",
    batch_number: "Batch Number",
    lot_number: "Lot Number",
    lot_release_number: "Lot Release Number",
    batch_lot_number: "Batch/Lot Number",
    packaging_lot: "Packaging Lot",

    //vatexempt
     usage: "Usage",
     date_publication: "Date of Publication/Effectivity",

     //Cosmetic_NN
     PROD_VARIANTS: "Product Variants",
     NOTIFICATION_DECISION_DATE: "Issuance Date",
     NOTIFICATION_VALIDITY: "Expiry Date",

     //cmdn
     CPR_NUMBER: "Registration Number",
     COMPANY_ADDRESS: "Address",
     AUTHORIZATION_TYPE: "Classification",
     DECISION_DATE: "Issuance Date",

     //cdrrgmp
     CERT_NUM: "Certificate Number",
     CATEGORY: "Category",
     VALIDITY_DATE: "Validity Date",
     PRODUCTS: "Products",
     MANUFACTURER_NAME: "Manufacturer",
     PLANT_ADDRESS: "Plant Address",
     IMPORTER_NAME: "Importer",
     TYPE_OF_APPLICATION: "Type of Application",
     PRODUCT_LINE: "Product Line",
     AUTHORIZED_PRODUCT_DOSAGE_FORM: "Authorized Product Line/Dosage Form",

     //cpr hup
     active_ingredient: "Active Ingredient",
     intended_use: "Intended Use",

     //cpr_huhs
     FERN_NO: "Registration Number",
     VARIANT_NAME: "Variant Name",
     ACTIVE_INGREDIENTS: "Active Ingredient",
     PRODUCT_CATEGORY_LABEL: "Product Category",
     PROD_SOURCE_EST_NAME: "Manufacturer",
     PROD_SOURCE_COUNTRY_LABEL: "Country of Origin",
     FERN_EXPIRY_HUMAN: "Expiry Date ",
     

     //tcca_notif
     PRODUCT_BRAND_NAME: "Brand Name",

     //food_gmp
     LTO_ACTIVITY:"Activity",
     ADDITIONAL_ACTIVITY: "Additional Activity",
     VALIDITY: "Validity Date",

     //HACCP Product
     PRODUCT:"Product",

     //fdawebsite advisory
     date_posted: "Date Published",
     title: "Title",
     category: "Category",
     post_link: "Post Link",

     //tcca products
    ITEM_NAME: "Item Name",
    ITEM_MODEL_NO: "Model No.",
    ROW: "Row",
    ITEM_SKU: "SKU",
    ITEM_AGE_GRADING_LABEL: "Age Grading"
    

  };

  const detailsFieldsMap = {
    lto_food: [
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_drugs: [
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_medicaldevice: [
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_healthrelateddevice: [
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_pco: [
      "SCOPE_OF_WORK",
      "PCO_METHOD",
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ISSUANCE_DATE",
      "LTO_VALIDITY",
    ],

    lto_cosmetics: [
      "ADDRESS",
      "REGION",
      "LTO_ACTIVITY_LABEL",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    otherEST: [
      "ADDRESS",
      "REGION",
      "LTO_ACTIVITY_LABEL",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_hup: [
      "ADDRESS",
      "REGION",
      "LTO_ACTIVITY_LABEL",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    lto_tcca: [
      "ADDRESS",
      "REGION",
      "LTO_ACTIVITY_LABEL",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    cdrr: [
      "classification",
      "packaging",
      "manufacturer",
      "trader",
      "importer",
      "distributor",
      "country_of_origin",
      "app_type",
      "issuance_date",
      "expiry_date",
      "pharmacologic_category",
    ],

    cpr_cdrrhr: [
      "manufacturer",
      "country_of_origin",
      "trader",
      "distributor",
      "issuance_date",
      "expiry_date", 
    ],

    healthcare_waste: [
      "intended_use_claim",
      "issuance_date",
      "expiry_date",    
    ],

    water_purification: [
      "intended_use_claim",
      "issuance_date",
      "expiry_date",    
    ],

     xray: [
      "address",
      "issuance_date",
      "expiry_date",    
    ],

    csl_batch: [
      "dosage_strength",
      "dosage_form",
      "lot_number",
      "issuance_date", 
      "expiry_date",      
    ],

    csl_lot: [
      "dosage_strength",
      "dosage_form",
      "packaging_lot",
      "issuance_date", 
      "expiry_date",      
    ],

    cosmetic_NN: [
      "PROD_VARIANTS",
      "NOTIFICATION_DECISION_DATE",
      "NOTIFICATION_VALIDITY",
    ],

     cmdn: [
      "COMPANY_ADDRESS",
      "AUTHORIZATION_TYPE",
    ],

    localcgmp: [
      "ADDRESS",
      "PRODUCTS",
    ],

    desktopForeigncgmp: [
      "VALIDITY_DATE",
      "TYPE_OF_APPLICATION",
      "PRODUCT_LINE",     
    ],

    inspectedForeign: [
      "VALIDITY_DATE",
      "TYPE_OF_APPLICATION",
      "PRODUCT_LINE",     
    ],

    PermitToRegister: [
      "PLANT_ADDRESS",
      "AUTHORIZED_PRODUCT_DOSAGE_FORM",    
    ],

    lto_huhs: [
      "ADDRESS",
      "REGION",
      "PRIMARY_ACTIVITY",
      "ADDITIONAL_ACTIVITIES",
      "ACCOMPLISHED_DATE",
      "LTO_VALIDITY",
    ],

    cpr_hup:[
      "active_ingredient",
      "intended_use",
      "country_of_origin",
      "issuance_date",
      "expiry_date",
    ],

    cpr_huhs: [
      "VARIANT_NAME",
      "ACTIVE_INGREDIENTS",
      "PRODUCT_CATEGORY_LABEL",
      "PROD_SOURCE_EST_NAME",
      "PROD_SOURCE_COUNTRY_LABEL",
      "ESTABLISHMENT_NAME",
    ],

    food_gmp: [
      "PLANT_ADDRESS",
      "LTO_ACTIVITY",
      "ADDITIONAL_ACTIVITY",
      "PRODUCTS",
    ],

    
    
  };

  // Columns per tab (override lto_cosmetics)
  const columnsMap = {
    default: ltoColumns,
    fdafoodproducts: fdaFoodColumns,
    cdrr: cdrrColumns,
    cpr_cdrrhr:cdrrhrcprColumns,
    healthcare_waste:healthcare_wasteColumns,
    water_purification: water_purificationColumns,
    xray:xrayColumns,
    csl_batch:cslbatchColumns,
    csl_lot:csllotColumns,
    vat_exempt: vat_exemptColumns,
    cosmetic_NN: cosmetic_NNColumns,
    cmdn: cmdnColumns,
    localcgmp: localcgmpColumns,
    desktopForeigncgmp: desktopForeigncgmpColumns,
    inspectedForeign: inspectedForeignColumns,
    PermitToRegister: PermitToRegisterColumns,
    cpr_hup: cprhupColumns,
    cpr_huhs: cprhuhsColumns,
    tcca_notif: tccanotifColumns,
    food_gmp: foodgmpColumns,
    HACCP: haccpColumns,
    HACCPprod: haccpprodColumns,
    fdawebsite: fdawebsiteColumns,
    tcca_notif_products: tccaProdColumns,
    lto_cosmetics: [
      "LTO_NUMBER",
      "ESTABLISHMENT_NAME",
      "ESTABLISHMENT_OWNER",
    ],

    lto_hup: [
      "LTO_NUMBER",
      "ESTABLISHMENT_NAME",
      "ESTABLISHMENT_OWNER",
    ],

    lto_tcca: [
      "LTO_NUMBER",
      "ESTABLISHMENT_NAME",
      "ESTABLISHMENT_OWNER",
    ],

    otherEST: [
      "LTO_NUMBER",
      "ESTABLISHMENT_NAME",
      "ESTABLISHMENT_OWNER",
    ],
  };

  // Tabs Configuration
  const tabConfig = [
    { key: "lto_food", label: "Food Industry", type: "expandable" },
    { key: "lto_drugs", label: "Drug Industry", type: "expandable" },
    { key: "lto_medicaldevice", label: "Medical Device Industry", type: "expandable" },
    { key: "lto_healthrelateddevice", label: "Health Related Device Industry", type: "expandable" },
    { key: "lto_pco", label: "Pest Control Operator", type: "expandable" },
    { key: "lto_cosmetics", label: "Cosmetic Industry", type: "expandable" },
    { key: "lto_hup", label: "HUP Industry", type: "expandable" },
    { key: "lto_tcca", label: "TCCA Industry", type: "expandable" },
    { key: "fdafoodproducts", label: "Food CPR", type: "simple" },
    { key: "cdrr", label: "Drug CPR", type: "expandable" },
    { key: "cpr_cdrrhr", label: "Medical Device CPR", type: "expandable" },
    { key: "healthcare_waste", label: "Healthcate Waste", type: "expandable" },
    { key: "water_purification", label: "Water Purification System", type: "expandable" },
    { key: "xray", label: "X-RAY Facilities", type: "expandable" },
    { key: "csl_batch", label: "Batch Notification", type: "expandable" },
    { key: "csl_lot", label: "Lot Release Certificate", type: "expandable" },
    { key: "vat_exempt", label: "Vat-Exempt Health Products", type: "simple" },
    { key: "cosmetic_NN", label: "Cosmetic Product Notification", type: "expandable" },
    { key: "cmdn", label: "Medical Device Notification", type: "expandable" },
    { key: "localcgmp", label: "Local cGMP", type: "expandable" },
    { key: "desktopForeigncgmp", label: "Desktop Foreign cGMP", type: "expandable" },
    { key: "inspectedForeign", label: "Inspected Foreign cGMP", type: "expandable" },
    { key: "PermitToRegister", label: "Permit to Register", type: "expandable" },
    { key: "lto_huhs", label: "HUHS Industry", type: "expandable" },
    { key: "cpr_hup", label: "HUP CPR", type: "expandable" },
    { key: "cpr_huhs", label: "HUHS CPR", type: "expandable" },
    { key: "tcca_notif", label: "TCCA Notification", type: "simple" },
    { key: "food_gmp", label: "Food GMP", type: "expandable" },
    { key: "HACCP", label: "HACCP Certificates", type: "simple" },
    { key: "HACCPprod", label: "HACCP Products", type: "simple" },
    { key: "otherEST", label: "Other Industry", type: "expandable" },
    { key: "fdawebsite", label: "FDA Advisory", type: "simple" },
    { key: "tcca_notif_products", label: "TCCA Products", type: "simple" },
  ];

  const isCentered = !hasSearched || loading;


  // Search Function

const normalizeToArray = (value) => {
  if (!value) return [];
  if (Array.isArray(value)) return value;
  if (typeof value === "object") return Object.values(value);
  return [];
};



 const handleSearch = async (searchQuery) => {
  const q = searchQuery ?? query;
  if (!q.trim()) return;

  setLoading(true);
  setHasSearched(false);

  // Reset results immediately (keeps UI predictable)
  setResults(
    Object.fromEntries(Object.keys(results).map((k) => [k, []]))
  );

  try {
    const res = await fetch(
      `http://127.0.0.1:8000/api/search?q=${encodeURIComponent(q)}`
    );

    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();

    // 🔥 GLOBAL NORMALIZATION FOR ALL TABS
    const normalizedResults = Object.fromEntries(
      Object.keys(results).map((key) => [
        key,
        normalizeToArray(data?.[key]),
      ])
    );

    setResults(normalizedResults);
    setHasSearched(true);

    // Select first tab that actually has data
    const firstTabWithResults =
      tabConfig.find(
        (tab) => normalizedResults[tab.key]?.length > 0
      )?.key || "lto_food";

    setActiveTab(firstTabWithResults);

    // Reset pagination per tab
    setPagination(
      Object.fromEntries(
        Object.keys(results).map((k) => [k, 1])
      )
    );

    // Reset expanded rows per tab
    setExpandedRows(
      Object.fromEntries(
        Object.keys(results).map((k) => [k, []])
      )
    );
  } catch (err) {
    console.error("Search failed:", err);
  } finally {
    setLoading(false);
  }
};



  // Voice Search

  const handleMicClick = () => {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
      alert("Your browser does not support speech recognition.");
      return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = "en-US";
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.start();
    setListening(true);

    recognition.onresult = (event) => {
      const spokenText = event.results[0][0].transcript;
      setQuery(spokenText);
      setListening(false);
      handleSearch(spokenText);
    };

    recognition.onerror = () => setListening(false);
    recognition.onspeechend = () => recognition.stop();
  };

  


  // JSX

  return (
    <div className="w-screen h-screen flex flex-col items-center bg-gray-50 p-4 transition-all duration-700">

      <div
        className={`flex flex-col items-center w-full max-w-3xl transition-all duration-700
          ${isCentered ? "justify-center h-screen mt-0" : "mt-6"}`}
      >
      <div className="flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-4 mb-6 w-full text-center sm:text-left">
  <a
  href="https://www.fda.gov.ph"
  target="_blank"
  rel="noopener noreferrer"
>
  <img
    src="https://www.fda.gov.ph/wp-content/uploads/2020/09/cropped-FDA-Web-Logo_Green_1.png"
    alt="FDA Logo"
    className="h-12 w-auto mb-2 sm:mb-0 cursor-pointer"
  />
</a>


  <h1 className="text-xl sm:text-2xl font-bold text-green-700 leading-snug">
    FOOD AND DRUG ADMINISTRATION <br />
    VERIFICATION PORTAL
  </h1>
</div>



        {/* Search Bar */}
       <div className="flex items-center w-full bg-white rounded-full shadow-md px-5 transition-transform duration-700">
          <input
  type="text"
  value={query}
  onChange={(e) => setQuery(e.target.value)}
  onKeyDown={(e) => {
    if (e.key === "Enter") handleSearch();
  }}
  placeholder="Type or Press mic icon to begin search"
  className="flex-grow py-3 px-2 focus:outline-none text-gray-700 font-semibold text-sm sm:text-base"
/>


          <div className="flex items-center space-x-2 pr-2">
           <button
              onClick={() => handleSearch()}
              className="text-gray-500 hover:text-green-700 transition-colors"
              title="Search"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
  <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
</svg>
            </button>
            <button
              onClick={handleMicClick}
              className={`transition-colors ${
                listening
                  ? "text-green-600 animate-pulse"
                  : "text-gray-500 hover:text-green-700"
              }`}
              title="Voice Search"
            >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-mic-fill" viewBox="0 0 16 16">
  <path d="M5 3a3 3 0 0 1 6 0v5a3 3 0 0 1-6 0z"/>
  <path d="M3.5 6.5A.5.5 0 0 1 4 7v1a4 4 0 0 0 8 0V7a.5.5 0 0 1 1 0v1a5 5 0 0 1-4.5 4.975V15h3a.5.5 0 0 1 0 1h-7a.5.5 0 0 1 0-1h3v-2.025A5 5 0 0 1 3 8V7a.5.5 0 0 1 .5-.5"/>
</svg>
            </button>
          </div>
        </div>

        {listening && <p className="mt-2 text-sm text-green-600 animate-pulse">Listening… Please speak now.</p>}
        {loading && <p className="mt-4 text-gray-600 italic animate-pulse">Searching...</p>}
      </div>

      {/* Tabs + Tables */}
      {hasSearched && !loading && (
        <div className="mt-6 w-full">

          {tabConfig.some(tab => results[tab.key]?.length > 0) ? (
            <>
             <div className="overflow-x-auto border rounded mb-4 bg-white">
  <table className="w-full border-collapse">
    <tbody>
      <tr>
        {tabConfig
          .filter(tab => results[tab.key]?.length > 0)
          .map(tab => (
            <td
              key={tab.key}
              className="border-r last:border-r-0 text-center"
            >
              <button
                onClick={() => setActiveTab(tab.key)}
                className={`
                  relative w-full px-4 py-3
                  transition-all duration-300 ease-out
                  ${
                    activeTab === tab.key
                      ? "text-[#286634] font-semibold bg-[#e8f6ee]"
                      : "text-[#000000] hover:text-[#FFFFFF] hover:bg-[#00833F]"
                  }
                `}
              >
                {tab.label} ({results[tab.key]?.length || 0})

                {/* Animated underline */}
                <span
                  className={`
                    absolute left-0 bottom-0 h-1 w-full
                    bg-[#00bf63]
                    transform transition-transform duration-300
                    origin-left
                    ${
                      activeTab === tab.key
                        ? "scale-x-100"
                        : "scale-x-0"
                    }
                  `}
                />
              </button>
            </td>
          ))}
      </tr>
    </tbody>
  </table>
</div>


              {tabConfig.map(tab => {
                if (activeTab !== tab.key) return null;

                const TableProps = {
                  key: tab.key,
                  title: `${tab.label} Records`,
                  data: results[tab.key],
                  labelMap,
                  page: pagination[tab.key],
                  setPage: p => setPagination(prev => ({ ...prev, [tab.key]: p })),
                  itemsPerPage,
                };

                if (tab.type === "expandable") {
                  return (
                    <ExpandableResultsTable
  {...TableProps}
  columns={columnsMap[tab.key] || ltoColumns}
  expandedRows={expandedRows[tab.key]}
  setExpandedRows={rows =>
    setExpandedRows(prev => ({ ...prev, [tab.key]: rows }))
  }
  detailsFields={detailsFieldsMap[tab.key]}
  sortConfig={sortConfig}
  onSort={handleSort}
/>

                  );
                }

                return (
  <SimpleResultsTable
    {...TableProps}
    columns={columnsMap[tab.key] || fdaFoodColumns}
    sortConfig={sortConfig}
    onSort={handleSort}
  />
);

              })}
            </>
          ) : (
            <p className="text-center text-gray-500 mt-10 text-lg">No Record Found</p>
          )}
        </div>        
      )}

      {/* Sticky Footer */}
      <footer className="mt-auto w-full bg-gray-50 border-t border-gray-200 py-3">
  <div className="flex items-center justify-between px-4">

    {/* Center text */}
    <div className="text-center flex-1">
      <p className="text-gray-400 text-sm italic">
        NOTE: The Information provided herein shall only be used for purposes of verification and shall in no case be used for any unlawful purpose.
      </p>
      <p className="text-gray-400 text-sm">
        © 2026 Food and Drug Administration Philippines. All Rights Reserved
      </p>
    </div>

     {/*Help icon*/}
    <span
      className="material-symbols-rounded text-gray-400 cursor-pointer hover:text-gray-600"
      title="Help"
    >
      <a
  href="https://forms.office.com/r/F2G6J0jYPJ"
  target="_blank"
  rel="noopener noreferrer"
  title="For additions or corrections"
  className="material-symbols-rounded text-gray-400 cursor-pointer hover:text-gray-600"
>
  help
</a>

    </span>

    {/* Spacer to balance layout */}
    <div className="w-6"></div>
  </div>

  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
  />
</footer>

    
    </div>
  );
}


// Expandable Table Component

function ExpandableResultsTable({
  title,
  columns,
  data,
  labelMap,
  page,
  setPage,
  itemsPerPage,
  expandedRows,
  setExpandedRows,
  detailsFields,
  sortConfig,
  onSort,
}) {

  const totalPages = Math.ceil(data.length / itemsPerPage);
  const startIndex = (page - 1) * itemsPerPage;
  const sortedData = React.useMemo(() => {
  if (!sortConfig.key) return data;

  return [...data].sort((a, b) => {
    const aVal = (a[sortConfig.key] || "").toString().toLowerCase();
    const bVal = (b[sortConfig.key] || "").toString().toLowerCase();

    if (aVal < bVal) return sortConfig.direction === "asc" ? -1 : 1;
    if (aVal > bVal) return sortConfig.direction === "asc" ? 1 : -1;
    return 0;
  });
}, [data, sortConfig]);

const currentData = sortedData.slice(startIndex, startIndex + itemsPerPage);

  const toggleExpand = (index) => {
    setExpandedRows(expandedRows.includes(index) ? expandedRows.filter(i => i !== index) : [...expandedRows, index]);
  };

  return (
    <div className="bg-white p-4 rounded-lg shadow mb-6">
      <h2 className="text-lg font-semibold text-green-700 mb-2">{title}</h2>
      <div className="overflow-x-auto">
        <table className="w-full border-collapse border border-gray-200 text-sm">
          <thead>
            <tr className="bg-green-700 text-white text-left">
              {columns.map(col => (
  <th
    key={col}
    onClick={() => SORTABLE_COLUMNS.includes(col) && onSort(col)}
    className={`p-2 border border-gray-300 ${
      SORTABLE_COLUMNS.includes(col)
        ? "cursor-pointer select-none hover:bg-green-600"
        : ""
    }`}
  >
    {labelMap[col] || col}
    {sortConfig.key === col && (
      <span className="ml-1">
        {sortConfig.direction === "asc" ? "▲" : "▼"}
      </span>
    )}
  </th>
))}
              <th className="p-2 border border-gray-300">Details</th>
            </tr>
          </thead>
          <tbody>
            {currentData.map((row, i) => {
              const globalIndex = startIndex + i;
              const isExpanded = expandedRows.includes(globalIndex);
              return (
                <React.Fragment key={globalIndex}>
                  <tr className="hover:bg-gray-100">
                    {columns.map(col => (
  <td key={col} className="p-2 border border-gray-300">
    {col === "post_link" && row[col] ? (
      <a
        href={row[col]}
        target="_blank"
        rel="noopener noreferrer"
        className="text-green-700 font-semibold hover:underline"
      >
        View Post
      </a>
    ) : (
      row[col] || "-"
    )}
  </td>
))}

                    <td className="p-2 border border-gray-300 text-center">
                      <button onClick={() => toggleExpand(globalIndex)} className="text-green-700 font-semibold hover:underline">{isExpanded ? "Hide Details" : "View Details"}</button>
                    </td>
                  </tr>
                  {isExpanded && (
                    <tr className="bg-gray-50">
                      <td colSpan={columns.length + 1} className="p-3">
                        {row && typeof row === "object" ? (
                          <div className="grid grid-cols-2 gap-2 text-sm">
                            {Object.entries(row).filter(([key]) => detailsFields.includes(key)).map(([key, value]) => (
                              <div key={key}><strong>{labelMap[key] || key}: </strong>{String(value || "-")}</div>
                            ))}
                          </div>
                        ) : <p className="text-gray-500 text-sm">No details available.</p>}
                      </td>
                    </tr>
                  )}
                </React.Fragment>
              );
            })}
          </tbody>
        </table>
      </div>

      {totalPages > 1 && (
        <div className="flex justify-between items-center mt-4">
          <button onClick={() => setPage(page - 1)} disabled={page === 1} className={`px-3 py-1 rounded ${page === 1 ? "bg-gray-300 cursor-not-allowed" : "bg-green-700 text-white hover:bg-green-800"}`}>Previous</button>
          <span className="text-gray-700">Page {page} of {totalPages}</span>
          <button onClick={() => setPage(page + 1)} disabled={page === totalPages} className={`px-3 py-1 rounded ${page === totalPages ? "bg-gray-300 cursor-not-allowed" : "bg-green-700 text-white hover:bg-green-800"}`}>Next</button>
        </div>
      )}
    </div>
  );
}


// Simple Table Component

function SimpleResultsTable({
  title,
  columns,
  data,
  labelMap,
  page,
  setPage,
  itemsPerPage,
  sortConfig,
  onSort,
}) {

  const totalPages = Math.ceil(data.length / itemsPerPage);
  const startIndex = (page - 1) * itemsPerPage;
  const sortedData = React.useMemo(() => {
  if (!sortConfig.key) return data;

  return [...data].sort((a, b) => {
    const aVal = (a[sortConfig.key] || "").toString().toLowerCase();
    const bVal = (b[sortConfig.key] || "").toString().toLowerCase();

    if (aVal < bVal) return sortConfig.direction === "asc" ? -1 : 1;
    if (aVal > bVal) return sortConfig.direction === "asc" ? 1 : -1;
    return 0;
  });
}, [data, sortConfig]);

const currentData = sortedData.slice(startIndex, startIndex + itemsPerPage);


  return (
    <div className="bg-white p-4 rounded-lg shadow mb-6">
      <h2 className="text-lg font-semibold text-green-700 mb-2">{title}</h2>
      <div className="overflow-x-auto">
        <table className="w-full border-collapse border border-gray-200 text-sm">
          <thead>
            <tr className="bg-green-700 text-white text-left">
              {columns.map(col => (
  <th
    key={col}
    onClick={() => SORTABLE_COLUMNS.includes(col) && onSort(col)}
    className={`p-2 border border-gray-300 ${
      SORTABLE_COLUMNS.includes(col)
        ? "cursor-pointer select-none hover:bg-green-600"
        : ""
    }`}
  >
    {labelMap[col] || col}
    {sortConfig.key === col && (
      <span className="ml-1">
        {sortConfig.direction === "asc" ? "▲" : "▼"}
      </span>
    )}
  </th>
))}

            </tr>
          </thead>
          <tbody>
            {currentData.map((row, i) => (
              <tr key={i} className="hover:bg-gray-100">
                {columns.map(col => (
  <td key={col} className="p-2 border border-gray-300">
    {col === "post_link" && row[col] ? (
      <a
        href={row[col]}
        target="_blank"
        rel="noopener noreferrer"
        className="text-green-700 font-semibold hover:underline"
      >
        View Post
      </a>
    ) : (
      row[col] || "-"
    )}
  </td>
))}

              </tr>
            ))}
          </tbody>
        </table>
      </div>

      

      {totalPages > 1 && (
        <div className="flex justify-between items-center mt-4">
          <button onClick={() => setPage(page - 1)} disabled={page === 1} className={`px-3 py-1 rounded ${page === 1 ? "bg-gray-300 cursor-not-allowed" : "bg-green-700 text-white hover:bg-green-800"}`}>Previous</button>
          <span className="text-gray-700">Page {page} of {totalPages}</span>
          <button onClick={() => setPage(page + 1)} disabled={page === totalPages} className={`px-3 py-1 rounded ${page === totalPages ? "bg-gray-300 cursor-not-allowed" : "bg-green-700 text-white hover:bg-green-800"}`}>Next</button>
        </div>
      )}


    </div>
  );
}

