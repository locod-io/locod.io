export interface Team {
  id: string;
  name: string;
}

export interface CacheIssue {
  id: string;
  identifier: string;
  title: string;
}

export interface Issue {
  id: string;
  identifier: string;
  title: string;
  state: State;
  description: string;
  assigneeId: string;
  assigneeName: string;
  createdAt: string;
  completedAt: string;
  archivedAt: string;
  subIssues: IssueCollection;
  comment: CommentCollection;
}

export interface State {
  id: string;
  name: string;
  type: string;
  color: string;
  position: string;
}

export interface IssueCollection {
  collection: Array<Issue>;
}

export interface CommentCollection {
  collection: Array<Comment>;
}

export interface Comment {
  id: string;
  body: string;
  createdAt: string;
  url: string;
  userId: string;
  userName: string;
  parentId: string;
  replies: CommentCollection;
}