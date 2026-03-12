تالين - واجهة أمامية لإدارة مخزون الأدوية (Bootstrap RTL)

ما تم إنشاؤه:
- صفحات HTML للوحة التحكم والوحدات: عملاء، موردون، منتجات، مخزون، مبيعات، مشتريات، تقارير، مستخدمون
- موارد CSS و JS بسيطة مع أمثلة على مخططات باستخدام Chart.js

تشغيل محلي:
1. افتح الملف `index.html` في متصفح أو استعمل خادم بسيط (مثل Live Server في VS Code).

الخطوات التالية المقترحة:
- توصيل الواجهات بواجهة خلفية (API) وتخزين بيانات حقيقية
- إضافة نماذج CRUD وعمليات التحقق
- تنفيذ صلاحيات وصول حقيقية على مستوى الواجهة والخادم

اقتراحات للخطوات التالية (عملية ومباشرة):

1) واجهة برمجة تطبيقات بسيطة (Mock API)
	- أنشئ خادم Node.js/Express صغير يقدم نقاط نهاية مثل `/api/customers`, `/api/products`, `/api/inventory`, `/api/sales`, `/api/purchases`.
	- استخدم json-server أو express مع ملف JSON لتجربة CRUD محليًا.

2) خرائط البيانات المقترحة
	- Customer: {id,name,type,region,phone,balance,notes}
	- Product: {id,name,manufacturer,strength,form,therapeutic_class,barcodes:[internal,external],batches:[{batch_no,sku,qty,production_date,expiry_date}]}
	- Inventory movements: {id,type,sku,batch,qty,source,target,date,notes}

3) صلاحيات ومصادقة
	- ابدأ بـ JWT على الخادم وواجهات اختبارية على الواجهة الأمامية.
	- صفح `users.html` يحتوي واجهة لتعيين الصلاحيات؛ ربطها بنموذج backend سيضمن تطبيقها فعليًا.

تشغيل سريع مع json-server (مثال محلي):
1. تثبيت json-server: `npm install -g json-server`
2. انشئ ملف `db.json` بجانب هذه الملفات ثم شغّل: `json-server --watch db.json --port 3000`
3. عدّل `assets/js/app.js` أو صفحات HTML لاستخدام `fetch('http://localhost:3000/products')` لاختبار CRUD.

إذا كنت تريد أعدّ لك مثال خادم mock كامل (Express or json-server) مع `db.json` جاهزًا، قل لي وسأنشئه هنا.

