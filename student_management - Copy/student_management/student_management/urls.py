
from django.contrib import admin
from django.urls import path
from django.conf import settings
from django.conf.urls.static import static


from . import views, HodViews

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', views.Dashboard, name="dashboard"),
    #path('dropbox', views.Dropbox, name="drobox"),
    path('login/', views.LOGIN, name="login"),
    path('logout/', views.doLogout, name="logout"),
    path('dologin',views.doLogin, name="doLogin"),
    path('hod/home',HodViews.index, name="hod_home"),
    #delete all
    path('delete/',views.DELETE, name="delete"),
    #profile update
    path('profile',views.PROFILE, name="profile"),
    path('profile/update',views.PROFILE_UPDATE, name="profile_update"),
    path('student/add',views.STUDENT_ADD, name="student_add"),
    path('student/save',views.STUDENT_SAVE, name="student_save"),
    path('student/subscribe',views.STUDENT_SUBSCRIBE, name="student_subscribe"),
    path('student/subscribe/new/',views.STUDENT_Modal, name="student_subscribe_new"),
    #path('student/subscribe/save', views.STUDENT_SUBSCRIBE_SAVE, name="student_subscribe_save"),
    # students 
    path('student/register',views.STUDENT_REGISTER, name="student_register"),
    path('student/register/save',views.REGISTRATION_SAVE, name="registration_save"),
    path('students',views.INDEX, name="students"),
    path('students/<int:id>',views.STUDENT_DETAIL, name="student_detail"),
    

]
if settings.DEBUG:
    urlpatterns += static(settings.STATIC_URL,document_root=settings.STATIC_ROOT)
    urlpatterns += static(settings.MEDIA_URL,document_root=settings.MEDIA_ROOT)