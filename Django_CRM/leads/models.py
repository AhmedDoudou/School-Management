from django.db import models
from django.db.models.signals import post_save
from django.contrib.auth.models import AbstractUser


class User(AbstractUser):
    is_organisor = models.BooleanField(default=True)
    is_agent = models.BooleanField(default=False)


class UserProfile(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)

    def __str__(self):
        return self.user.username



class LeadManager(models.Manager):
    def get_queryset(self):
        return super().get_queryset()


class Lead(models.Model):
    first_name = models.CharField( max_length=50)
    last_name  = models.CharField( max_length=50)
    age        = models.IntegerField(default=0)
    organisation = models.ForeignKey(UserProfile, on_delete=models.CASCADE)
    agent      = models.ForeignKey("Agent", null=True, blank=True, on_delete=models.SET_NULL)
    category = models.ForeignKey("Category", related_name="leads", null=True, blank=True, on_delete=models.SET_NULL)
    description = models.TextField()
    date_added = models.DateTimeField(auto_now_add=True)
    phone_number = models.CharField(max_length=20)
    email = models.EmailField()
    profile_picture = models.ImageField(null=True, blank=True,upload_to='profiles/')

    objects = LeadManager()

    #def __str__(self):
        #return self.first_name
        #f"{self.first_name} {self.agent}"

def handle_upload_followups(instance,filename):
    return f"lead_followups/lead_{instance.id}/{filename}"

class FollowUp(models.Model):
    lead = models.ForeignKey(Lead,related_name='followups' ,on_delete=models.CASCADE)
    date_added = models.DateTimeField(auto_now_add=True)
    note = models.TextField(null=True, blank=True)
    files = models.FileField(null=True, blank=True, upload_to=handle_upload_followups)


class Agent(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    organisation = models.ForeignKey(UserProfile, on_delete=models.CASCADE)

    def __str__(self):
        return self.user.username


class Category(models.Model):
    name = models.CharField(max_length=30)
    organisation = models.ForeignKey(UserProfile, on_delete=models.CASCADE)

    def __str__(self):
        return self.name


def post_user_created_signal(sender,instance, created,**kwargs):
    if created:
        UserProfile.objects.create(user=instance)
    

post_save.connect(post_user_created_signal, sender=User)