"""project URL Configuration

The `urlpatterns` list routes URLs to views. For more information please see:
    https://docs.djangoproject.com/en/4.0/topics/http/urls/
Examples:
Function views
    1. Add an import:  from my_app import views
    2. Add a URL to urlpatterns:  path('', views.home, name='home')
Class-based views
    1. Add an import:  from other_app.views import Home
    2. Add a URL to urlpatterns:  path('', Home.as_view(), name='home')
Including another URLconf
    1. Import the include() function: from django.urls import include, path
    2. Add a URL to urlpatterns:  path('blog/', include('blog.urls'))
"""
from django.contrib import admin
from django.conf import settings
from django.urls import path, include
from django.conf.urls.static import static

from django.contrib.auth.views import LoginView,LogoutView 
from core.views import DashbordView
from core import views
from inscriptions import views
 



urlpatterns = [
    path('admin/', admin.site.urls),
    path("student/", include('core.urls', namespace="student")),
    path("program/", include('programs.urls', namespace="program")),
    path("contact/", include('contacts.urls', namespace="contact")),
    path("inscription/", include('inscriptions.urls', namespace="inscription")),
    path("subscription/", include('subscriptions.urls', namespace="subscription")),
    path("", DashbordView.as_view(), name="home"),
    path("login/", LoginView.as_view(), name="login"),
    path("logout/", LogoutView.as_view(), name="logout"),
    path("newinscription/",views.NewInscriptionCreateView, name="newinscription"),
    


]

if settings.DEBUG:
    urlpatterns += static(settings.STATIC_URL,document_root=settings.STATIC_ROOT)
    urlpatterns += static(settings.MEDIA_URL,document_root=settings.MEDIA_ROOT)
