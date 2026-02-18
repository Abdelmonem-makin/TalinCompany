<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>غير مصرح لك بالوصول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 600px;
            margin: 5rem auto;
            text-align: center;
        }
        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-title {
            color: #495057;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .btn-home {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-home:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon">
                🚫
            </div>
            <h1 class="error-title">غير مصرح لك بالوصول</h1>
            <p class="error-message">
                عذراً، لا تمتلك الصلاحيات اللازمة للوصول إلى هذه الصفحة.
                يرجى التواصل مع مدير النظام إذا كنت تعتقد أن هذا خطأ.
            </p>
            <a href="{{ url('/') }}" class="btn btn-home btn-lg">العودة إلى الصفحة الرئيسية</a>
        </div>
    </div>
</body>
</html>
