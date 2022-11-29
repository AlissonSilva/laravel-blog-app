import 'package:app/models/user.dart';

class Post {
  int? id;
  String? body;
  String? image;
  int? likesCount;
  int? commentCount;
  User? user;
  bool? selfLiked;

  Post(
      {this.id,
      this.body,
      this.image,
      this.likesCount,
      this.commentCount,
      this.user,
      this.selfLiked});

  factory Post.fromJson(Map<String, dynamic> json) {
    return Post(
        id: json['id'],
        body: json['body'],
        image: json['image'],
        likesCount: json['likes_count'],
        commentCount: json['comments_count'],
        selfLiked: json['likes'].length > 0,
        user: User(
            id: json['user']['id'],
            name: json['user']['name'],
            image: json['user']['image']));
  }
}
