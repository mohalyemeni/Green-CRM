function confirmLogout() {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم إنهاء جلستك الحالية في النظام!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f06548', // لون زر التأكيد (أحمر يتناسب مع Velzon)
        cancelButtonColor: '#878a99', // لون زر الإلغاء (رمادي)
        confirmButtonText: 'نعم، تسجيل خروج',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        // إذا ضغط المستخدم على "نعم"
        if (result.isConfirmed) {
            // إرسال الفورم برمجياً
            document.getElementById('logout-form').submit();
        }
    });
}
