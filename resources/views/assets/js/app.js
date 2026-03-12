// Demo charts for dashboard
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
  new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'],
      datasets: [{ label: 'المبيعات', data: [1200,1500,1300,1700,1600,1900,2200,2100,2300,2000,2400,2500], borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)' }]
    },
    options: { responsive: true }
  });
}
const stockCtx = document.getElementById('stockChart');
if (stockCtx) {
  new Chart(stockCtx, {
    type: 'doughnut',
    data: { labels: ['مضادات حيوية','مسكنات','فيتامينات','أخرى'], datasets: [{ data: [40,25,20,15], backgroundColor: ['#198754','#0d6efd','#ffc107','#6c757d'] }] },
    options: { responsive: true }
  });
}

// ---------------------------
// Simple in-browser store (localStorage)
// ---------------------------
const Store = (function(){
  const prefix = 'talin_';
  function _key(k){ return prefix + k; }
  function load(k){ try { return JSON.parse(localStorage.getItem(_key(k))||'null')||[] } catch(e){ return []; } }
  function save(k, v){ localStorage.setItem(_key(k), JSON.stringify(v||[])); }
  function insert(k, item){ const arr = load(k); item.id = item.id || Date.now().toString(36); arr.push(item); save(k,arr); return item; }
  function update(k, id, patch){ const arr = load(k); const i = arr.findIndex(x=>x.id==id); if (i===-1) return null; arr[i] = {...arr[i], ...patch}; save(k,arr); return arr[i]; }
  function remove(k,id){ let arr = load(k); arr = arr.filter(x=>x.id!=id); save(k,arr); }
  return {load,save,insert,update,remove}
})();

// Wire up forms already present in pages
document.addEventListener('DOMContentLoaded', function(){
  // Render inventory movements table if present
  const movementsTable = document.querySelector('#movementsTable tbody');
  const movementsFilter = document.getElementById('movementsFilter');
  function renderMovements(){
    if (!movementsTable) return;
    const all = Store.load('inventory').concat(Store.load('disposals').map(d=>({...d,type:'dispose'})));
    const filter = movementsFilter ? movementsFilter.value : 'all';
    const rows = all.filter(m=> filter==='all' ? true : m.type===filter);
    movementsTable.innerHTML = '';
    if (!rows.length) { movementsTable.innerHTML = '<tr><td colspan="6" class="text-center">لا توجد حركات</td></tr>'; return; }
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      const date = r.date || r.created_at || new Date().toISOString();
      const who = r.supplier || r.customer || r.productName || '';
      const skuBatch = (r.sku||'') + (r.batch?('/'+r.batch):'');
      const qty = r.qty || r.quantity || r.qty_in || r.qty_out || '';
      tr.innerHTML = `<td>${date}</td><td>${r.type}</td><td>${who}</td><td>${skuBatch}</td><td>${qty}</td><td>${r.notes||''}</td>`;
      movementsTable.appendChild(tr);
    });
  }
  if (movementsFilter) movementsFilter.addEventListener('change', renderMovements);
  renderMovements();
  // Customers
  const saveCustomerBtn = document.getElementById('saveCustomerBtn');
  if (saveCustomerBtn){ saveCustomerBtn.addEventListener('click', function(){
    const form = document.getElementById('customerForm'); if (!form.reportValidity()) return;
    const data = Object.fromEntries(new FormData(form)); Store.insert('customers', data);
    bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide(); alert('تم حفظ العميل محليًا'); window.location.reload();
  }); }

  // Suppliers
  const saveSupplierBtn = document.getElementById('saveSupplierBtn');
  if (saveSupplierBtn){ saveSupplierBtn.addEventListener('click', function(){ const form=document.getElementById('supplierForm'); if(!form.reportValidity()) return; Store.insert('suppliers', Object.fromEntries(new FormData(form))); bootstrap.Modal.getInstance(document.getElementById('addSupplierModal')).hide(); alert('تم حفظ المورد محليًا'); window.location.reload(); }); }

  // Products
  const saveProductBtn = document.getElementById('saveProductBtn');
  if (saveProductBtn){ saveProductBtn.addEventListener('click', function(){ const form=document.getElementById('productForm'); if(!form.reportValidity()) return; const obj=Object.fromEntries(new FormData(form)); obj.batches = obj.batch_no? [{batch_no: obj.batch_no, production_date: obj.production_date, expiry_date: obj.expiry_date, qty: 0}]: []; Store.insert('products', obj); bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide(); alert('تم حفظ المنتج محليًا'); window.location.reload(); }); }

  // Receive / Issue (Inventory movements)
  const saveReceiveBtn = document.getElementById('saveReceiveBtn');
  if (saveReceiveBtn){ saveReceiveBtn.addEventListener('click', function(){ const form=document.getElementById('receiveForm'); if(!form.reportValidity()) return; const obj=Object.fromEntries(new FormData(form)); obj.type='receive'; Store.insert('inventory', obj); bootstrap.Modal.getInstance(document.getElementById('receiveModal')).hide(); alert('تم تسجيل الاستلام محليًا'); window.location.reload(); }); }
  const saveIssueBtn = document.getElementById('saveIssueBtn');
  if (saveIssueBtn){ saveIssueBtn.addEventListener('click', function(){ const form=document.getElementById('issueForm'); if(!form.reportValidity()) return; const obj=Object.fromEntries(new FormData(form)); obj.type='issue'; Store.insert('inventory', obj); bootstrap.Modal.getInstance(document.getElementById('issueModal')).hide(); alert('تم تسجيل الصرف محليًا'); window.location.reload(); }); }

  // Sales/Purchases forms
  const saveQuoteBtn = document.getElementById('saveQuoteBtn'); if (saveQuoteBtn) saveQuoteBtn.addEventListener('click', ()=>{ const form=document.getElementById('quoteForm'); if(!form.reportValidity()) return; const obj=Object.fromEntries(new FormData(form)); obj.type='quote'; Store.insert('sales',obj); bootstrap.Modal.getInstance(document.getElementById('quoteModal')).hide(); alert('تم حفظ عرض السعر محليًا'); });
  const saveInvoiceBtn = document.getElementById('saveInvoiceBtn'); if (saveInvoiceBtn) saveInvoiceBtn.addEventListener('click', ()=>{ const form=document.getElementById('invoiceForm'); if(!form.reportValidity()) return; const obj=Object.fromEntries(new FormData(form)); obj.type='invoice'; Store.insert('sales',obj); bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide(); alert('تم حفظ الفاتورة محليًا'); });
  const savePoBtn = document.getElementById('savePoBtn'); if (savePoBtn) savePoBtn.addEventListener('click', ()=>{ const form=document.getElementById('poForm'); if(!form.reportValidity()) return; Store.insert('purchases', Object.fromEntries(new FormData(form))); bootstrap.Modal.getInstance(document.getElementById('poModal')).hide(); alert('تم إرسال طلب الشراء محليًا'); });
  const savePurchaseInvoiceBtn = document.getElementById('savePurchaseInvoiceBtn'); if (savePurchaseInvoiceBtn) savePurchaseInvoiceBtn.addEventListener('click', ()=>{ const form=document.getElementById('purchaseInvoiceForm'); if(!form.reportValidity()) return; Store.insert('purchases', Object.fromEntries(new FormData(form))); bootstrap.Modal.getInstance(document.getElementById('invoicePurchaseModal')).hide(); alert('تم حفظ فاتورة الشراء محليًا'); });

  // Roles
  const saveRoleBtn = document.getElementById('saveRoleBtn'); if (saveRoleBtn) saveRoleBtn.addEventListener('click', function(e){ e.preventDefault(); const role=document.getElementById('roleSelect').value; const perms=Array.from(document.querySelectorAll('#rolesForm .form-check-input')).map(cb=>({id:cb.id,checked:cb.checked})); Store.update('roles', role, {perms}); alert('تم حفظ الصلاحيات محليًا (تجريبي)'); });

  // Render simple counts on dashboard
  const elSalesCount = document.querySelector('.card .display-6');
  try{
    const totalProducts = Store.load('products').length;
    const totalCustomers = Store.load('customers').length;
    const totalSuppliers = Store.load('suppliers').length;
    // quick badge update
    const pCard = document.querySelectorAll('.card .display-6');
    if (pCard && pCard.length>=2) pCard[1].textContent = totalProducts + ' أصناف';
  } catch(e){}
});

// Accounting: receivables/payables/payments and P&L
document.addEventListener('DOMContentLoaded', function(){
  // Only run when accounting page elements exist
  const receivablesTable = document.querySelector('#receivablesTable tbody');
  const payablesTable = document.querySelector('#payablesTable tbody');
  const paymentsTable = document.querySelector('#paymentsTable tbody');
  const plSummary = document.getElementById('plSummary');
  const savePaymentBtn = document.getElementById('savePaymentBtn');

  function loadAccounting(){
    if (!receivablesTable) return;
    // sales store contains quotes/invoices; treat invoices as receivables
    const sales = Store.load('sales').filter(s=>s.type==='invoice');
    receivablesTable.innerHTML = '';
    sales.forEach((s,idx)=>{
      const total = computeInvoiceTotal(s);
      const payments = Store.load('payments').filter(p=>p.refId==s.id && p.type==='receive');
      const paid = payments.reduce((a,b)=>a+Number(b.amount||0),0);
  const tr = document.createElement('tr');
  tr.innerHTML = `<td>${s.id||''}</td><td>${s.date||''}</td><td>${s.client||''}</td><td>${total}</td><td>${paid}</td><td>${(total-paid).toFixed(2)}</td><td><button class="btn btn-sm btn-outline-primary" data-ref="${s.id}" onclick="openPaymentModal('${s.id}','receive')">تحصيل</button> <button class="btn btn-sm btn-outline-secondary ms-1" onclick="openStatement('${s.client}','customer','${s.client}')">كشف حساب</button></td>`;
      receivablesTable.appendChild(tr);
    });

    // payables from purchases
    const purchases = Store.load('purchases');
    payablesTable.innerHTML = '';
    purchases.forEach((p,idx)=>{
      const total = computePurchaseTotal(p);
      const payments = Store.load('payments').filter(x=>x.refId==p.id && x.type==='pay');
      const paid = payments.reduce((a,b)=>a+Number(b.amount||0),0);
  const tr = document.createElement('tr');
  tr.innerHTML = `<td>${p.id||''}</td><td>${p.date||''}</td><td>${p.supplier||''}</td><td>${total}</td><td>${paid}</td><td>${(total-paid).toFixed(2)}</td><td><button class="btn btn-sm btn-outline-secondary" onclick="openStatement('${p.supplier}','supplier','${p.supplier}')">كشف حساب</button></td>`;
      payablesTable.appendChild(tr);
    });

    // payments log
    const payments = Store.load('payments') || [];
    paymentsTable.innerHTML = '';
    payments.forEach(p=>{ const tr=document.createElement('tr'); tr.innerHTML=`<td>${p.date||''}</td><td>${p.type}</td><td>${p.party||p.supplier||p.customer||''}</td><td>${p.amount}</td><td>${p.notes||''}</td>`; paymentsTable.appendChild(tr); });

    // P&L: sum sales totals - sum purchases cost
    const totalSales = sales.reduce((a,s)=>a+Number(computeInvoiceTotal(s)||0),0);
    const totalPurchases = purchases.reduce((a,p)=>a+Number(computePurchaseTotal(p)||0),0);
    const profit = totalSales - totalPurchases;
    if (plSummary) plSummary.innerHTML = `إجمالي المبيعات: ${totalSales.toFixed(2)}<br>إجمالي المشتريات (تكلفة): ${totalPurchases.toFixed(2)}<br><strong>صافي الربح: ${profit.toFixed(2)}</strong>`;
  }

  window.openPaymentModal = function(refId, type){
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const form = document.getElementById('paymentForm'); form.refId.value = refId; form.type.value = type; modal.show();
  }

  // Open account statement for a party (customer or supplier)
  window.openStatement = function(partyId, partyType, partyName){
    // partyType: 'customer' or 'supplier'
    const modal = new bootstrap.Modal(document.getElementById('statementModal'));
    document.getElementById('statementTitle').textContent = `كشف حساب: ${partyName}`;
    const tbody = document.querySelector('#statementTable tbody'); tbody.innerHTML = '';
    // collect invoices/purchases and payments
    const rows = [];
    if (partyType==='customer'){
      const sales = Store.load('sales').filter(s=>s.type==='invoice' && (s.client==partyName || s.client==partyId));
      sales.forEach(s=> rows.push({date: s.date||'', type:'invoice', ref: s.id, desc: 'فاتورة مبيعات', debit: computeInvoiceTotal(s), credit:0}));
      const payments = Store.load('payments').filter(p=>p.type==='receive' && (p.refId==null || p.refId==s?.id || p.party==partyName));
      payments.forEach(p=> rows.push({date:p.date, type:'payment', ref: p.id, desc:p.notes||'دفعة', debit:0, credit:Number(p.amount||0)}));
    } else {
      const purchases = Store.load('purchases').filter(p=> (p.supplier==partyName || p.supplier==partyId));
      purchases.forEach(p=> rows.push({date: p.date||'', type:'purchase', ref: p.id, desc: 'فاتورة شراء', debit: computePurchaseTotal(p), credit:0}));
      const payments = Store.load('payments').filter(p=>p.type==='pay' && (p.refId==null || p.party==partyName));
      payments.forEach(p=> rows.push({date:p.date, type:'payment', ref: p.id, desc:p.notes||'دفعة', debit:0, credit:Number(p.amount||0)}));
    }
    // sort by date
    rows.sort((a,b)=> new Date(a.date||0) - new Date(b.date||0));
    let balance = 0;
    rows.forEach(r=>{
      balance += (Number(r.debit||0) - Number(r.credit||0));
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${r.date}</td><td>${r.type}</td><td>${r.ref||''}</td><td>${r.desc}</td><td>${r.debit||''}</td><td>${r.credit||''}</td><td>${balance.toFixed(2)}</td>`;
      tbody.appendChild(tr);
    });
    // wire pay button
    const stmtPayBtn = document.getElementById('statementPayBtn'); stmtPayBtn.onclick = function(){
      // open payment modal prefilled
      const payModal = new bootstrap.Modal(document.getElementById('paymentModal'));
      const form = document.getElementById('paymentForm'); form.refId.value = '';
      form.type.value = partyType==='customer' ? 'receive' : 'pay'; form.notes.value = `دفعة من ${partyName}`; payModal.show();
    };
    modal.show();
  }

  function computeInvoiceTotal(inv){
    try{ const items = JSON.parse(inv.items||'[]'); return items.reduce((s,i)=>s + (Number(i.qty||0) * Number(i.price||0)),0); } catch(e){ return 0; }
  }
  function computePurchaseTotal(p){ try{ const items = JSON.parse(p.items||'[]'); return items.reduce((s,i)=>s + (Number(i.qty||0) * Number(i.cost||0)),0); } catch(e){ return 0; } }

  if (savePaymentBtn){ savePaymentBtn.addEventListener('click', function(){ const form = document.getElementById('paymentForm'); if (!form.reportValidity()) return; const data = Object.fromEntries(new FormData(form)); data.id = Date.now().toString(36); data.date = new Date().toISOString(); data.party = data.type==='receive' ? (document.querySelector(`#receivablesTable button[data-ref="${data.refId}"]`)?.closest('tr')?.children[2]?.innerText) : (document.querySelector(`#payablesTable button[data-ref="${data.refId}"]`)?.closest('tr')?.children[2]?.innerText);
    Store.insert('payments', data); bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide(); loadAccounting(); alert('تم تسجيل الدفعة'); }); }

  // Export P&L
  const exportPL = document.getElementById('exportPL'); if (exportPL){ exportPL.addEventListener('click', function(){ const sales = Store.load('sales').filter(s=>s.type==='invoice'); const purchases = Store.load('purchases'); const rows = [['نوع','رقم','المبلغ']]; sales.forEach(s=> rows.push(['مبيعات', s.id||'', computeInvoiceTotal(s)])); purchases.forEach(p=> rows.push(['مشتريات', p.id||'', computePurchaseTotal(p)])); const csv = rows.map(r=> r.map(c=> '"'+String(c).replace(/"/g,'""')+'"').join(',')).join('\n'); const blob = new Blob([csv], {type:'text/csv'}); const url=URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='pl.csv'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url); }); }

  // init
  loadAccounting();
  const refreshBtn = document.getElementById('refreshAccounting'); if (refreshBtn) refreshBtn.addEventListener('click', loadAccounting);
  // Seed demo accounting data button
  const seedBtn = document.getElementById('seedDemoAccounting');
  if (seedBtn){ seedBtn.addEventListener('click', function(){
    const invoice = { id: 'INV'+Date.now().toString(36), date: new Date().toISOString(), client: 'مستشفى الرحمة', type: 'invoice', items: JSON.stringify([{sku:'PARA500',qty:200,price:20}]) };
    Store.insert('sales', invoice);
    const po = { id: 'PO'+Date.now().toString(36), date: new Date().toISOString(), supplier: 'شركة أدوية تالين', items: JSON.stringify([{sku:'PARA500',qty:300,cost:10}]) };
    Store.insert('purchases', po);
    Store.insert('payments', { id: 'PAY'+Date.now().toString(36), date: new Date().toISOString(), type: 'receive', refId: invoice.id, amount: 100, notes: 'دفعة تجريبية', party: invoice.client });
    alert('تم إنشاء بيانات تجريبية');
    loadAccounting();
  }); }
});

// Expiry detection and disposal logic
function parseDateISO(s){ if(!s) return null; const d=new Date(s); return isNaN(d)?null:d; }
function isExpired(dateStr){ const d=parseDateISO(dateStr); if(!d) return false; const today=new Date(); today.setHours(0,0,0,0); return d < today; }

function findExpiredBatches(){
  const products = Store.load('products');
  const expired = [];
  products.forEach(p=>{
    const sku = p.id || p.internal_barcode || p.name;
    (p.batches||[]).forEach(b=>{
      if (b.expiry_date && isExpired(b.expiry_date)) expired.push({productName: p.name, sku: sku, productId: p.id, batch: b.batch_no, expiry: b.expiry_date, qty: b.qty||0});
    });
  });
  return expired;
}

document.addEventListener('DOMContentLoaded', function(){
  const manageBtn = document.getElementById('manageExpiredBtn');
  if (manageBtn){
    manageBtn.addEventListener('click', function(){
      const rows = findExpiredBatches();
      const tbody = document.querySelector('#expiredBatchesTable tbody'); tbody.innerHTML = '';
      if (!rows.length){ tbody.innerHTML = '<tr><td colspan="6" class="text-center">لا توجد باتشات منتهية</td></tr>'; }
      rows.forEach(r=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${r.productName}</td><td>${r.sku}</td><td>${r.batch}</td><td>${r.expiry}</td><td>${r.qty}</td><td><input class="form-control form-control-sm" type="number" min="0" max="${r.qty}" value="${r.qty}" data-product-id="${r.productId}" data-batch="${r.batch}"></td>`;
        tbody.appendChild(tr);
      });
      const modal = new bootstrap.Modal(document.getElementById('expiredModal'));
      modal.show();
    });
  }

  const disposeBtn = document.getElementById('disposeBatchesBtn');
  if (disposeBtn){ disposeBtn.addEventListener('click', function(){
    const inputs = Array.from(document.querySelectorAll('#expiredBatchesTable tbody input'));
    const disposals = [];
    inputs.forEach(inp=>{
      const qty = Number(inp.value||0); if (qty<=0) return;
      const pid = inp.getAttribute('data-product-id'); const batchNo = inp.getAttribute('data-batch');
      // update product batch qty
      const products = Store.load('products');
      const p = products.find(x=>x.id==pid);
      if (!p) return;
      const b = (p.batches||[]).find(x=>x.batch_no==batchNo);
      if (!b) return;
      const removed = Math.min(b.qty||0, qty);
      b.qty = (b.qty||0) - removed;
      disposals.push({productId: pid, productName: p.name, batch: batchNo, qty: removed, date: new Date().toISOString()});
      // if batch qty zero, keep batch with zero (or optionally remove)
      Store.update('products', pid, {batches: p.batches});
    });
    if (disposals.length){
      // log disposals
      const log = Store.load('disposals');
      const newLog = log.concat(disposals);
      Store.save('disposals', newLog);
      alert('تم تسجيل التخلص من الأصناف المنتهية');
      window.location.reload();
    } else {
      alert('لم تُحدد كميات للتخلص');
    }
  }); }
});

// CSV export helper
function tableToCSV(table){
  const rows = Array.from(table.querySelectorAll('tr'));
  return rows.map(r=>Array.from(r.querySelectorAll('th,td')).map(c=> '"'+(c.innerText.replace(/"/g,'""'))+'"').join(',')).join('\n');
}
document.addEventListener('DOMContentLoaded', function(){
  const exportBtns = document.querySelectorAll('[data-export-table]');
  exportBtns.forEach(btn=> btn.addEventListener('click', function(){
    const sel = btn.getAttribute('data-export-table');
    const table = document.querySelector(sel);
    if (!table) return alert('لم يتم العثور على جدول للتصدير');
    const csv = tableToCSV(table);
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = 'report.csv'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
  }));
});
