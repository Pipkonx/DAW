/**
 * Abstracción de Estilos y Elementos UI Bootstrap/Icons
 * Centraliza el diseño y los iconos para mantener los templates 100% limpios.
 */
const UI = {
    // Layout & Containers
    container: 'container mt-5 animate__animated animate__fadeIn',
    containerFluid: 'container-fluid',
    mainCard: 'card shadow-sm border-0',
    cardBody: 'card-body',
    cardHeaderDark: 'card-header bg-dark text-white',
    cardHeaderPrimary: 'card-header bg-primary text-white',
    
    // Auth / Login
    bgLogin: 'bg-login d-flex align-items-center justify-content-center',
    cardLogin: 'card shadow-lg border-0',
    
    // Grid & Flex
    row: 'row',
    rowG3: 'row g-3',
    rowG4: 'row g-4 mt-5',
    col12: 'col-12',
    colMd2: 'col-md-2',
    colMd3: 'col-md-3',
    colMd4: 'col-md-4',
    colMd8: 'col-md-8',
    flexBetween: 'd-flex justify-content-between align-items-center',
    flexCenter: 'd-flex align-items-center',
    gap2: 'd-flex gap-2',
    
    // Buttons
    btnPrimary: 'btn btn-primary',
    btnSuccess: 'btn btn-success',
    btnDanger: 'btn btn-danger',
    btnInfo: 'btn btn-info',
    btnDark: 'btn btn-dark',
    btnOutlinePrimary: 'btn btn-outline-primary btn-sm',
    btnOutlineInfo: 'btn btn-outline-info btn-sm',
    btnOutlineDanger: 'btn btn-outline-danger btn-sm',
    btnOutlineLight: 'btn btn-outline-light btn-sm',
    btnLarge: 'btn btn-primary btn-lg fw-bold w-100 py-3',
    btnLink: 'btn btn-link btn-sm p-0',
    btnGroup: 'btn-group',
    
    // Forms
    formFloating: 'form-floating mb-3',
    formControl: 'form-control',
    formSelect: 'form-select',
    formLabel: 'form-label small fw-bold',
    
    // Tables
    table: 'table table-hover mb-0',
    tableHeader: 'table-dark',
    tableResponsive: 'table-responsive bg-white shadow-sm rounded',
    
    // Components
    navbar: 'navbar navbar-expand-lg navbar-dark bg-dark shadow-sm',
    navLink: 'nav-link cursor-pointer',
    navLinkActive: 'active fw-bold',
    navBrand: 'navbar-brand fw-bold',
    spinner: 'spinner-border text-primary',
    jumbotron: 'p-5 bg-white rounded shadow-sm border text-center',
    cardAction: 'p-4 border rounded cursor-pointer hover-shadow',
    badgeAdmin: 'badge bg-danger ms-1',
    badgeStatus: 'badge w-100',
    hr: 'my-4',
    
    // Text & Spacing
    title: 'display-5 fw-bold text-primary',
    subtitle: 'lead text-muted mt-3',
    sectionTitle: 'fw-bold mb-4',
    textMuted: 'small text-muted',
    textSuccess: 'text-success',
    textDanger: 'text-danger',
    mb3: 'mb-3',
    mb4: 'mb-4',
    mt3: 'mt-3',
    mt4: 'mt-4',
    fwBold: 'fw-bold',
    
    // Iconos (Abstraídos para evitar clases bi- sueltas)
    iconLogin: 'bi bi-shield-lock-fill text-primary display-1 mb-4',
    iconPeople: 'bi bi-people',
    iconPeoplePrimary: 'bi bi-people text-primary fs-1',
    iconPolicy: 'bi bi-file-earmark-text',
    iconPolicySuccess: 'bi bi-file-earmark-text text-success fs-1',
    iconStats: 'bi bi-bar-chart-line text-info fs-1',
    iconHome: 'bi bi-house-door',
    iconPlus: 'bi bi-person-plus',
    iconEye: 'bi bi-eye',
    iconTrash: 'bi bi-trash',
    iconBack: 'bi bi-arrow-left',
    iconCash: 'bi bi-cash-stack',
    iconUser: 'bi bi-person-circle',
    iconShield: 'bi bi-shield-check text-primary'
};
